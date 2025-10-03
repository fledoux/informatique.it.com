<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Exception;

class ImapService
{
    private $connection;
    private ?string $host;
    private int $port;
    private ?string $username;
    private ?string $password;
    private string $encryption;
    private string $folder;
    private string $archiveFolder;
    private string $folderSeparator;

    public function __construct()
    {
        $this->host = config('imap.host');
        $this->port = config('imap.port', 993);
        $this->username = config('imap.username');
        $this->password = config('imap.password');
        $this->encryption = config('imap.encryption', 'ssl');
        $this->folder = config('imap.folder', 'INBOX');
        $this->folderSeparator = config('imap.folder_separator', '.');
        
        // Le dossier d'archives sera défini après la connexion
        $this->archiveFolder = '';
    }

    /**
     * Établit la connexion IMAP
     */
    public function connect(): bool
    {
        // Vérifier la configuration
        if (empty($this->host) || empty($this->username) || empty($this->password)) {
            Log::error('IMAP configuration incomplete. Please check your .env file.');
            return false;
        }

        try {
            $mailbox = "{{$this->host}:{$this->port}/imap/{$this->encryption}/novalidate-cert}";
            
            $this->connection = imap_open($mailbox, $this->username, $this->password);
            
            if (!$this->connection) {
                Log::error('IMAP connection failed: ' . imap_last_error());
                return false;
            }
            
            // Construire le dossier d'archives avec le séparateur configuré
            $archivePattern = config('imap.archive_folder_pattern', 'Archives.{year}');
            $this->archiveFolder = str_replace('{year}', date('Y'), $archivePattern);
            
            Log::info('IMAP connection established successfully');
            Log::info("Archive folder will be: {$this->archiveFolder}");
            return true;
        } catch (Exception $e) {
            Log::error('IMAP connection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ferme la connexion IMAP
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            imap_close($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Récupère et traite les nouveaux emails
     */
    public function fetchNewEmails(): int
    {
        if (!$this->connection) {
            Log::error('No IMAP connection available');
            return 0;
        }

        $processed = 0;
        
        try {
            // Rechercher les emails non lus
            $emails = imap_search($this->connection, 'UNSEEN');
            
            if (!$emails) {
                Log::info('No new emails found');
                return 0;
            }

            Log::info('Found ' . count($emails) . ' new emails');

            foreach ($emails as $emailId) {
                try {
                    if ($this->processEmail($emailId)) {
                        $processed++;
                        
                        // Déplacer vers le dossier Archives selon la configuration
                        if (config('imap.move_to_archive', true)) {
                            $this->moveEmailToArchive($emailId);
                        } else {
                            // Sinon marquer simplement comme lu
                            imap_setflag_full($this->connection, $emailId, "\\Seen");
                        }
                    }
                } catch (Exception $e) {
                    Log::error("Error processing email ID {$emailId}: " . $e->getMessage());
                }
            }

            Log::info("Successfully processed {$processed} emails");
            
        } catch (Exception $e) {
            Log::error('Error fetching emails: ' . $e->getMessage());
        }

        return $processed;
    }

    /**
     * Traite un email individuel
     */
    private function processEmail(int $emailId): bool
    {
        try {
            // Récupérer les headers
            $header = imap_headerinfo($this->connection, $emailId);
            
            if (!$header) {
                Log::error("Could not get header for email ID {$emailId}");
                return false;
            }

            // Extraire les informations de l'expéditeur
            $from = $header->from[0] ?? null;
            if (!$from) {
                Log::error("No sender found for email ID {$emailId}");
                return false;
            }

            $senderEmail = strtolower($from->mailbox . '@' . $from->host);
            $senderName = $from->personal ?? $senderEmail;

            // Décoder le nom si nécessaire
            if ($senderName && $senderName !== $senderEmail) {
                $senderName = $this->decodeHeader($senderName);
            }

            // Récupérer le sujet
            $subject = $header->subject ?? 'Pas de sujet';
            $subject = $this->decodeHeader($subject);

            // Récupérer l'ID unique du message
            $messageId = $header->message_id ?? null;

            // Vérifier si on a déjà traité cet email
            if ($messageId && Ticket::where('email_message_id', $messageId)->exists()) {
                Log::info("Email already processed, skipping: {$messageId}");
                return true; // Marquer comme traité pour éviter de le reprocesser
            }

            // Récupérer le contenu du message
            $body = $this->getEmailBody($emailId);

            // Trouver ou créer l'utilisateur
            $user = $this->findOrCreateUser($senderEmail, $senderName);
            
            if (!$user) {
                Log::error("Could not find or create user for email: {$senderEmail}");
                return false;
            }

            // Créer le ticket
            $ticket = $this->createTicketFromEmail($user, $subject, $body, $messageId);

            if ($ticket) {
                Log::info("Created ticket #{$ticket->id} from email: {$senderEmail} - {$subject}");
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error("Error processing email ID {$emailId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère le contenu du message
     */
    private function getEmailBody(int $emailId): string
    {
        $body = '';
        
        try {
            // Essayer de récupérer le contenu HTML d'abord
            $structure = imap_fetchstructure($this->connection, $emailId);
            
            if (isset($structure->parts) && count($structure->parts)) {
                // Email multipart
                for ($i = 0; $i < count($structure->parts); $i++) {
                    $part = $structure->parts[$i];
                    
                    if ($part->subtype === 'PLAIN') {
                        $body = imap_fetchbody($this->connection, $emailId, $i + 1);
                        $body = $this->decodeBody($body, $part->encoding);
                        break;
                    } elseif ($part->subtype === 'HTML') {
                        $htmlBody = imap_fetchbody($this->connection, $emailId, $i + 1);
                        $htmlBody = $this->decodeBody($htmlBody, $part->encoding);
                        // Convertir HTML en texte brut
                        $body = strip_tags($htmlBody);
                        break;
                    }
                }
            } else {
                // Email simple
                $body = imap_body($this->connection, $emailId);
                if ($structure->encoding) {
                    $body = $this->decodeBody($body, $structure->encoding);
                }
            }

            // Nettoyer le contenu
            $body = trim($body);
            if (empty($body)) {
                $body = 'Contenu de l\'email non disponible';
            }

        } catch (Exception $e) {
            Log::error("Error getting email body for ID {$emailId}: " . $e->getMessage());
            $body = 'Erreur lors de la récupération du contenu de l\'email';
        }

        return $body;
    }

    /**
     * Décode l'encodage du contenu
     */
    private function decodeBody(string $body, int $encoding): string
    {
        switch ($encoding) {
            case 1: // 8bit
                return $body;
            case 2: // binary
                return $body;
            case 3: // base64
                return base64_decode($body);
            case 4: // quoted-printable
                return quoted_printable_decode($body);
            default:
                return $body;
        }
    }

    /**
     * Décode les headers encodés
     */
    private function decodeHeader(string $header): string
    {
        $decoded = imap_mime_header_decode($header);
        $result = '';
        
        foreach ($decoded as $part) {
            $charset = $part->charset ?? 'default';
            if ($charset === 'default') {
                $result .= $part->text;
            } else {
                $result .= mb_convert_encoding($part->text, 'UTF-8', $charset);
            }
        }
        
        return trim($result);
    }

    /**
     * Trouve ou crée un utilisateur basé sur l'email
     */
    private function findOrCreateUser(string $email, string $name): ?User
    {
        // Chercher un utilisateur existant
        $user = User::where('email', $email)->first();
        
        if ($user) {
            return $user;
        }

        // Si pas trouvé, chercher une entreprise par défaut pour les nouveaux utilisateurs
        $defaultCompany = Company::where('name', 'like', '%support%')
            ->orWhere('name', 'like', '%default%')
            ->orWhere('name', 'like', '%client%')
            ->first();

        if (!$defaultCompany) {
            // Créer une entreprise par défaut si elle n'existe pas
            $defaultCompany = Company::create([
                'name' => 'Clients Email',
                'status' => 'active',
                'address' => '',
                'postal_code' => '',
                'city' => '',
                'country' => 'FR'
            ]);
        }

        // Séparer prénom et nom si possible
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Créer le nouvel utilisateur
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'company_id' => $defaultCompany->id,
                'status' => 'active',
                'password' => bcrypt(str()->random(32)), // Mot de passe aléatoire
                'email_verified_at' => now(), // Marquer comme vérifié
            ]);

            // Assigner le rôle utilisateur par défaut
            $user->assignRole('user');

            Log::info("Created new user from email: {$email} - {$name}");
            return $user;

        } catch (Exception $e) {
            Log::error("Error creating user for email {$email}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crée un ticket à partir d'un email
     */
    private function createTicketFromEmail(User $user, string $subject, string $body, ?string $messageId): ?Ticket
    {
        try {
            $ticket = Ticket::create([
                'subject' => $subject,
                'question' => $body,
                'priority' => config('imap.default_priority', 'normal'),
                'status' => 'new',
                'company_id' => $user->company_id,
                'author_id' => $user->id,
                'billable' => true,
                'source' => 'email',
                'email_message_id' => $messageId,
                'folder_code' => '',
            ]);

            return $ticket;

        } catch (Exception $e) {
            Log::error("Error creating ticket from email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Déplace un email vers le dossier d'archives
     */
    private function moveEmailToArchive(int $emailId): bool
    {
        try {
            // Créer le dossier d'archives si nécessaire
            if (!$this->ensureArchiveFolderExists()) {
                Log::error("Cannot create archive folder, marking email as read instead");
                imap_setflag_full($this->connection, $emailId, "\\Seen");
                return true; // Continue le traitement
            }

            // Marquer comme lu avant de déplacer
            imap_setflag_full($this->connection, $emailId, "\\Seen");
            
            // Déplacer l'email
            $result = imap_mail_move($this->connection, $emailId, $this->archiveFolder);
            
            if ($result) {
                // Expunge pour finaliser le déplacement
                imap_expunge($this->connection);
                Log::info("Email {$emailId} marked as read and moved to {$this->archiveFolder}");
                return true;
            } else {
                Log::error("Failed to move email {$emailId}: " . imap_last_error());
                Log::info("Email {$emailId} was marked as read but could not be moved");
                return true; // Email traité et marqué comme lu même si pas déplacé
            }
        } catch (Exception $e) {
            Log::error("Error moving email {$emailId} to archive: " . $e->getMessage());
            // Fallback: marquer comme lu
            imap_setflag_full($this->connection, $emailId, "\\Seen");
            return true;
        }
    }

    /**
     * Détecte le séparateur de dossier du serveur IMAP
     */
    private function detectFolderSeparator(): void
    {
        try {
            $folders = imap_list($this->connection, "{{$this->host}:{$this->port}/imap/{$this->encryption}/novalidate-cert}", "*");
            
            if ($folders) {
                foreach ($folders as $folder) {
                    if (str_contains($folder, '.')) {
                        $this->folderSeparator = '.';
                        break;
                    } elseif (str_contains($folder, '/')) {
                        $this->folderSeparator = '/';
                        break;
                    }
                }
            }
            
            Log::info("Detected folder separator: '{$this->folderSeparator}'");
        } catch (Exception $e) {
            Log::warning("Could not detect folder separator, using default '.': " . $e->getMessage());
            $this->folderSeparator = '.';
        }
    }

    /**
     * S'assure que le dossier d'archives existe
     */
    private function ensureArchiveFolderExists(): bool
    {
        try {
            $mailboxPrefix = "{{$this->host}:{$this->port}/imap/{$this->encryption}/novalidate-cert}";
            
            // Lister les dossiers existants
            $folders = imap_list($this->connection, $mailboxPrefix, "*");
            $existingFolders = [];
            
            if ($folders) {
                foreach ($folders as $folder) {
                    // Extraire le nom du dossier (après le })
                    if (strpos($folder, '}') !== false) {
                        $folderName = substr($folder, strpos($folder, '}') + 1);
                        $existingFolders[] = $folderName;
                    }
                }
            }
            
            Log::info("Existing folders: " . implode(', ', $existingFolders));
            
            // Vérifier si le dossier d'archives complet existe déjà
            if (in_array($this->archiveFolder, $existingFolders)) {
                Log::info("Archive folder {$this->archiveFolder} already exists");
                return true;
            }
            
            // Essayer de créer le dossier d'archives complet
            $archiveMailbox = $mailboxPrefix . $this->archiveFolder;
            
            if (imap_createmailbox($this->connection, $archiveMailbox)) {
                Log::info("Successfully created archive folder: {$this->archiveFolder}");
                return true;
            } else {
                $error = imap_last_error();
                Log::error("Failed to create archive folder {$this->archiveFolder}: {$error}");
                
                // Essayer avec un nom simple sans séparateur
                $simpleFolder = 'Archives' . date('Y');
                $this->archiveFolder = $simpleFolder;
                
                if (imap_createmailbox($this->connection, $mailboxPrefix . $simpleFolder)) {
                    Log::info("Created simple archive folder: {$simpleFolder}");
                    return true;
                }
                
                return false;
            }

        } catch (Exception $e) {
            Log::error("Error ensuring archive folder exists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtient les statistiques des dossiers d'archives
     */
    public function getArchiveStats(): array
    {
        if (!$this->connection) {
            return [];
        }

        try {
            $stats = [];
            $folders = imap_list($this->connection, "{{$this->host}:{$this->port}/imap/{$this->encryption}/novalidate-cert}", "Archives/*");
            
            if ($folders) {
                foreach ($folders as $folder) {
                    // Extraire le nom du dossier
                    $folderName = substr(strrchr($folder, '}'), 1);
                    
                    // Se connecter au dossier et obtenir les statistiques
                    $archiveConnection = imap_open($folder, $this->username, $this->password);
                    if ($archiveConnection) {
                        $info = imap_mailboxmsginfo($archiveConnection);
                        $stats[$folderName] = [
                            'total_messages' => $info->Nmsgs ?? 0,
                            'size' => round(($info->Size ?? 0) / 1024 / 1024, 2) // MB
                        ];
                        imap_close($archiveConnection);
                    }
                }
            }
            
            return $stats;
        } catch (Exception $e) {
            Log::error('Error getting archive stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Teste la connexion IMAP
     */
    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        try {
            if ($this->connect()) {
                $result['success'] = true;
                $result['message'] = 'Connexion IMAP réussie';
                
                // Obtenir des informations sur la boîte mail
                $mailboxInfo = imap_mailboxmsginfo($this->connection);
                $result['details'] = [
                    'total_messages' => $mailboxInfo->Nmsgs ?? 0,
                    'unread_messages' => $mailboxInfo->Unread ?? 0,
                    'recent_messages' => $mailboxInfo->Recent ?? 0,
                ];
                
                // Obtenir les statistiques des archives
                $archiveStats = $this->getArchiveStats();
                if (!empty($archiveStats)) {
                    $result['details']['archive_folders'] = $archiveStats;
                }
                
                $this->disconnect();
            } else {
                $result['message'] = 'Échec de la connexion IMAP: ' . imap_last_error();
            }
        } catch (Exception $e) {
            $result['message'] = 'Erreur de connexion: ' . $e->getMessage();
        }

        return $result;
    }
}