<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnauthorizedReplyMail;
use Exception;

class ImapService
{
    private $connection;
    private ?string $host;
    private int $port;
    private ?string $username;
    private ?string $password;
    private string $encryption;
    private string $archiveFolder;
    private array $messages = []; // Pour collecter les messages à afficher
    
    // Compteurs pour les statistiques
    private int $processedCount = 0;
    private int $rejectedCount = 0;
    private int $archivedCount = 0;

    public function __construct()
    {
        $this->host = config('imap.host');
        $this->port = config('imap.port', 993);
        $this->username = config('imap.username');
        $this->password = config('imap.password');
        $this->encryption = config('imap.encryption', 'ssl');
        
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

        // Réinitialiser les compteurs
        $this->processedCount = 0;
        $this->rejectedCount = 0;
        $this->archivedCount = 0;
        
        try {
            // Rechercher les emails non lus en utilisant les UIDs (SE_UID)
            $emails = imap_search($this->connection, 'UNSEEN', SE_UID);
            
            if (!$emails) {
                Log::info('Aucun nouvel email trouvé');
                return 0;
            }

            $totalEmails = count($emails);
            Log::info("Found {$totalEmails} new email(s)");
            $this->addMessage("📬 {$totalEmails} nouvel(aux) email(s) trouvé(s)");

            foreach ($emails as $uid) {
                $emailProcessed = false;
                
                try {
                    // Convertir UID en numéro de message pour le traitement
                    $msgNo = imap_msgno($this->connection, $uid);
                    
                    if (!$msgNo) {
                        Log::warning("Could not get message number for UID {$uid}");
                        // Archiver quand même pour éviter de le retraiter
                        $this->rejectedCount++;
                    } else {
                        $emailProcessed = $this->processEmail($msgNo);
                        
                        if ($emailProcessed) {
                            $this->processedCount++;
                        } else {
                            $this->rejectedCount++;
                        }
                    }
                    
                } catch (Exception $e) {
                    Log::error("Error processing email UID {$uid}: " . $e->getMessage());
                    $this->addMessage("❌ Erreur lors du traitement de l'email UID #{$uid}");
                    $this->rejectedCount++;
                } finally {
                    // TOUJOURS archiver l'email (succès, rejet ou erreur) pour éviter de le retraiter
                    try {
                        if (config('imap.move_to_archive', true)) {
                            $this->moveEmailToArchiveByUid($uid);
                            $this->archivedCount++;
                        } else {
                            // Marquer comme lu en utilisant l'UID
                            imap_setflag_full($this->connection, $uid, "\\Seen", ST_UID);
                        }
                    } catch (Exception $archiveError) {
                        Log::error("Failed to archive email UID {$uid}: " . $archiveError->getMessage());
                    }
                }
            }

            // Message de résumé
            if ($this->processedCount > 0) {
                $this->addMessage("✅ {$this->processedCount} email(s) traité(s) avec succès");
            }
            if ($this->rejectedCount > 0) {
                $this->addMessage("⚠️  {$this->rejectedCount} email(s) rejeté(s) (utilisateur non inscrit)");
            }
            if ($this->archivedCount > 0) {
                $this->addMessage("📁 {$this->archivedCount} email(s) archivé(s)");
            }

            Log::info("Email processing summary: {$this->processedCount} processed, {$this->rejectedCount} rejected, {$this->archivedCount} archived");
            
        } catch (Exception $e) {
            Log::error('Error fetching emails: ' . $e->getMessage());
        }

        return $this->processedCount;
    }

    /**
     * Récupère les messages collectés pour affichage dans le terminal
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Ajoute un message pour affichage dans le terminal
     */
    private function addMessage(string $message): void
    {
        $this->messages[] = $message;
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

            // Ignorer les emails provenant de notre propre adresse (éviter les boucles)
            $ourEmail = strtolower(config('app.company.emails.help'));
            if ($senderEmail === $ourEmail) {
                Log::info("Ignoring email from our own address: {$senderEmail}");
                return true; // Marquer comme traité pour l'archiver
            }

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

            // Extraire les pièces jointes UNE SEULE FOIS pour tous les cas
            $structure = imap_fetchstructure($this->connection, $emailId);
            $attachments = [];
            if ($structure) {
                $attachments = $this->extractAttachments($emailId, $structure);
                if (!empty($attachments)) {
                    Log::info("Found " . count($attachments) . " attachment(s) in email from {$senderEmail}");
                }
            }

            // Vérifier s'il y a un code de réponse dans l'email
            $replyCode = \App\Services\EmailReplyCodeService::extractReplyCode($body);
            
            if ($replyCode) {
                // C'est une réponse à un ticket existant
                Log::info("Reply code found in email", ['code' => $replyCode, 'sender' => $senderEmail]);
                
                // SÉCURITÉ : Valider le code avec l'email de l'expéditeur
                $existingTicket = \App\Services\EmailReplyCodeService::getTicketFromReplyCode($replyCode, $senderEmail);
                
                if ($existingTicket) {
                    // Nettoyer le contenu (supprimer tout après le code)
                    $cleanedBody = \App\Services\EmailReplyCodeService::cleanEmailContent($body);
                    
                    // Ajouter un message au ticket existant avec les pièces jointes
                    return $this->addMessageToTicket($existingTicket, $senderEmail, $cleanedBody, $attachments);
                } else {
                    Log::warning("Invalid reply code or unauthorized sender", ['code' => $replyCode, 'sender' => $senderEmail]);
                    // Traiter comme un nouveau ticket si le code est invalide ou l'expéditeur non autorisé
                }
            }

            // Pas de code de réponse ou code invalide - SÉCURITÉ: Vérifier si l'utilisateur existe
            $user = $this->findExistingUser($senderEmail);
            
            if (!$user) {
                Log::warning("SÉCURITÉ: Tentative de création de ticket par utilisateur non-inscrit: {$senderEmail}");
                
                // Envoyer un mail d'erreur pour inciter à s'inscrire
                try {
                    //Mail::to($senderEmail)->send(new UnauthorizedReplyMail($senderEmail, 'NOUVEAU'));
                    Log::info("✅ Mail de rejet envoyé à l'utilisateur non-inscrit: {$senderEmail}");
                    $this->addMessage("✅ Mail de rejet envoyé à l'utilisateur non-inscrit: {$senderEmail}");
                } catch (Exception $e) {
                    Log::error("❌ Échec envoi mail de rejet à {$senderEmail}: " . $e->getMessage());
                    $this->addMessage("❌ Échec envoi mail de rejet à {$senderEmail}: " . $e->getMessage());
                }
                
                return false;
            }

            // L'utilisateur existe - créer le ticket avec les pièces jointes (déjà extraites)
            $ticket = $this->createTicketFromEmail($user, $subject, $body, $messageId, $attachments);

            if ($ticket) {
                Log::info("Created Support n°{$ticket->id} from email: {$senderEmail} - {$subject}");
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
                        // Convertir les retours à la ligne en HTML
                        $body = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
                        break;
                    } elseif ($part->subtype === 'HTML') {
                        $htmlBody = imap_fetchbody($this->connection, $emailId, $i + 1);
                        $htmlBody = $this->decodeBody($htmlBody, $part->encoding);
                        // Nettoyer le HTML mais le garder
                        $body = $this->cleanHtml($htmlBody);
                        break;
                    }
                }
            } else {
                // Email simple
                $body = imap_body($this->connection, $emailId);
                if ($structure->encoding) {
                    $body = $this->decodeBody($body, $structure->encoding);
                }
                
                // Détecter si c'est du HTML ou du texte brut
                if (preg_match('/<html|<body|<p>|<div>/i', $body)) {
                    // C'est du HTML
                    $body = $this->cleanHtml($body);
                } else {
                    // C'est du texte brut, convertir les retours à la ligne
                    $body = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
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
     * Nettoie le HTML des emails en gardant les balises essentielles
     */
    private function cleanHtml(string $html): string
    {
        // Supprimer les scripts, styles et autres éléments dangereux
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
        
        // Garder uniquement les balises sûres
        $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><div><span>';
        $html = strip_tags($html, $allowedTags);
        
        // Nettoyer les attributs dangereux (onclick, onerror, etc.)
        $html = preg_replace('/ on\w+="[^"]*"/i', '', $html);
        
        return trim($html);
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
     * SÉCURITÉ: Ne trouve QUE les utilisateurs existants - ne crée JAMAIS d'utilisateur
     */
    private function findExistingUser(string $email): ?User
    {
        // Chercher UNIQUEMENT un utilisateur existant
        return User::where('email', $email)->first();
    }

    /**
     * Ajoute un message à un ticket existant
     */
    private function addMessageToTicket(Ticket $ticket, string $senderEmail, string $messageContent, array $attachments = []): bool
    {
        try {
            Log::info("Processing reply for Support n°{$ticket->id}", [
                'sender' => $senderEmail,
                'attachments_count' => count($attachments)
            ]);
            
            // Trouver l'utilisateur qui envoie la réponse
            $user = User::where('email', $senderEmail)->first();
            
            if (!$user) {
                Log::warning("User not found for reply email: {$senderEmail}");
                
                // Envoyer un mail d'erreur pour inciter à s'inscrire
                try {
                    //Mail::to($senderEmail)->send(new UnauthorizedReplyMail($senderEmail, $ticket->id));
                    Log::info("Unauthorized reply email sent to: {$senderEmail}");
                } catch (Exception $e) {
                    Log::error("Failed to send unauthorized reply email to {$senderEmail}: " . $e->getMessage());
                }
                
                return false;
            }

            // VÉRIFICATION DE SÉCURITÉ : L'utilisateur doit appartenir à la même société que le ticket
            // EXCEPTION : Les membres du support (company_id = 1) peuvent répondre à tous les tickets
            if ($user->company_id !== $ticket->company_id && $user->company_id !== 1) {
                Log::warning("Security: User from different company trying to reply to ticket", [
                    'user_email' => $senderEmail,
                    'user_company_id' => $user->company_id,
                    'ticket_id' => $ticket->id,
                    'ticket_company_id' => $ticket->company_id
                ]);
                
                // Envoyer un mail d'erreur pour sécurité
                try {
                    //Mail::to($senderEmail)->send(new UnauthorizedReplyMail($senderEmail, $ticket->id));
                    Log::info("✅ Mail de rejet de sécurité envoyé à: {$senderEmail}");
                    $this->addMessage("✅ Mail de rejet de sécurité envoyé à: {$senderEmail}");
                } catch (Exception $e) {
                    Log::error("❌ Échec envoi mail de rejet de sécurité à {$senderEmail}: " . $e->getMessage());
                    $this->addMessage("❌ Échec envoi mail de rejet de sécurité à {$senderEmail}: " . $e->getMessage());
                }
                
                return false;
            }

            // Si le ticket est clôturé (resolved, closed, canceled), créer un nouveau ticket
            if (in_array($ticket->status, ['resolved', 'closed', 'canceled'])) {
                Log::info("Ticket n°{$ticket->id} is closed ({$ticket->status}), creating new ticket");
                
                $newTicket = Ticket::create([
                    'status' => 'new',
                    'priority' => $ticket->priority,
                    'company_id' => $ticket->company_id,
                    'author_id' => $user->id,
                    'assigned_to' => $ticket->assigned_to,
                    'folder_code' => $ticket->folder_code,
                    'subject' => 'Re: ' . $ticket->subject,
                    'question' => "Suite à la demande #" . $ticket->id . " :\n\n" . ($ticket->question ?? ''),
                    'billable' => $ticket->billable,
                    'source' => 'email',
                ]);
                
                // Créer le message dans le nouveau ticket
                $ticketMessage = \App\Models\TicketMessage::create([
                    'status' => 'active',
                    'subject' => 'Re: ' . $ticket->subject,
                    'body' => $messageContent,
                    'company_id' => $ticket->company_id,
                    'ticket_id' => $newTicket->id,
                    'author_id' => $user->id,
                ]);
                
                // Uploader les pièces jointes dans le nouveau ticket
                if (!empty($attachments)) {
                    Log::info("Traitement de " . count($attachments) . " fichier(s) pour le nouveau ticket #{$newTicket->id}");
                    
                    $attachmentService = new \App\Services\AttachmentService();
                    $uploadedAttachments = $attachmentService->uploadEmailAttachments(
                        $attachments,
                        $newTicket->id,
                        $newTicket->company_id,
                        $user->id,
                        $ticketMessage->id
                    );
                    
                    Log::info("✅ " . count($uploadedAttachments) . " fichier(s) uploadé(s) pour le nouveau ticket #{$newTicket->id}");
                }
                
                // Envoyer l'email de confirmation pour le nouveau ticket
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TicketConfirmationMail($newTicket));
                    Log::info("Confirmation email sent for new Support n°{$newTicket->id} to {$user->email}");
                    $this->addMessage("📧 Email de confirmation envoyé à {$user->email}");
                } catch (Exception $e) {
                    Log::error("Failed to send confirmation email for new Support n°{$newTicket->id}: " . $e->getMessage());
                    $this->addMessage("⚠️  Échec envoi email de confirmation");
                }
                
                Log::info("Created new Support n°{$newTicket->id} from closed ticket n°{$ticket->id}");
                $this->addMessage("✅ Support n°{$newTicket->id} créé (réponse au ticket clôturé n°{$ticket->id})");
                
                return true;
            }

            // Créer un nouveau message pour le ticket existant (non clôturé)
            $ticketMessage = \App\Models\TicketMessage::create([
                'status' => 'active', // Message public, visible par le client
                'subject' => 'Re: ' . $ticket->subject,
                'body' => $messageContent,
                'company_id' => $ticket->company_id, // Utiliser la company_id du ticket (sécurité)
                'ticket_id' => $ticket->id,
                'author_id' => $user->id,
            ]);

            // Uploader les pièces jointes si présentes
            if (!empty($attachments)) {
                Log::info("Traitement de " . count($attachments) . " fichier(s) pour le message #{$ticketMessage->id}");
                
                $attachmentService = new \App\Services\AttachmentService();
                $uploadedAttachments = $attachmentService->uploadEmailAttachments(
                    $attachments,
                    $ticket->id,
                    $ticket->company_id,
                    $user->id,
                    $ticketMessage->id
                );
                
                Log::info("✅ " . count($uploadedAttachments) . " fichier(s) uploadé(s) pour le message #{$ticketMessage->id}");
            }

            // Mettre à jour le statut du ticket
            $ticket->update([
                'status' => 'in_progress',
                'updated_at' => now()
            ]);

            // Déterminer qui a répondu : support ou client
            $isSupport = $user->company_id === 1;
            
            // Récupérer les utilisateurs du support avec email activé
            $supportUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['email']);
                });
            
            if ($isSupport) {
                // Le support a répondu → Envoyer le récap au CLIENT + Notifier le SUPPORT
                Log::info("📧 Support replied, sending recap to client + notifying support team", [
                    'support_user' => $user->email,
                    'client_email' => $ticket->author->email,
                    'ticket_id' => $ticket->id
                ]);
                
                // 1. Envoyer récap au client
                try {
                    //Mail::to($ticket->author->email)->send(new \App\Mail\TicketClientRecapMail($ticket, $ticketMessage));
                    Log::info("✅ Client recap email sent to {$ticket->author->email} for ticket #{$ticket->id}");
                } catch (Exception $e) {
                    Log::error("❌ Failed to send client recap email: " . $e->getMessage());
                }
                
                // 2. Notifier l'équipe support
                foreach ($supportUsers as $supportUser) {
                    try {
                        //Mail::to($supportUser->email)->send(new \App\Mail\TicketReplyNotificationMail($ticket, $ticketMessage, $supportUser->email));
                        Log::info("✅ Support notification sent to {$supportUser->email} for ticket #{$ticket->id}");
                    } catch (Exception $e) {
                        Log::error("❌ Failed to send notification to {$supportUser->email}: " . $e->getMessage());
                    }
                }
            } else {
                // Le client a répondu → Notifier le SUPPORT + Envoyer récap au CLIENT
                Log::info("📧 Client replied, notifying support team + sending recap to client", [
                    'client_user' => $user->email,
                    'ticket_id' => $ticket->id
                ]);
                
                // 1. Notifier l'équipe support
                foreach ($supportUsers as $supportUser) {
                    try {
                        //Mail::to($supportUser->email)->send(new \App\Mail\TicketReplyNotificationMail($ticket, $ticketMessage, $supportUser->email));
                        Log::info("✅ Support notification sent to {$supportUser->email} for ticket #{$ticket->id}");
                    } catch (Exception $e) {
                        Log::error("❌ Failed to send notification to {$supportUser->email}: " . $e->getMessage());
                    }
                }
                
                // 2. Envoyer récap au client (auto-send)
                try {
                    Mail::to($ticket->author->email)
                        ->send(new \App\Mail\TicketClientRecapMail($ticket, $ticketMessage));
                    Log::info("✅ Client recap email (auto) sent to {$ticket->author->email} for ticket #{$ticket->id}");
                } catch (Exception $e) {
                    Log::error("❌ Failed to send client recap email: " . $e->getMessage());
                }
            }
            
            // Récupérer les utilisateurs avec Pushover activé
            $pushoverUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['sms']);
                });
            
            // Envoyer notification Pushover si au moins un utilisateur l'a activée
            if ($pushoverUsers->count() > 0) {
                $pushoverMessage = "🔔 Nouvelle réponse par email sur Support #{$ticket->id}\n";
                $pushoverMessage .= "Client : {$ticket->author->name}\n";
                $pushoverMessage .= "Sujet : {$ticket->subject}\n";
                $pushoverMessage .= "Voir : " . route('ticket.show', $ticket->id);
                
                \App\Helpers\Helper::sendPushoverNotification('Nouvelle réponse client', $pushoverMessage);
                Log::info("Pushover notification sent for ticket #{$ticket->id} to {$pushoverUsers->count()} user(s)");
            }

            Log::info("✅ Message #{$ticketMessage->id} ajouté au Support n°{$ticket->id} avec " . count($attachments) . " fichier(s)");
            return true;

        } catch (Exception $e) {
            Log::error("Error adding message to Support n°{$ticket->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un ticket à partir d'un email
     */
    private function createTicketFromEmail(User $user, string $subject, string $body, ?string $messageId, array $attachments = []): ?Ticket
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
            ]);

            // Sauvegarder les pièces jointes sur S3
            Log::info("createTicketFromEmail: received " . count($attachments) . " attachment(s)");
            if (!empty($attachments)) {
                Log::info("Calling AttachmentService for Support n°{$ticket->id}");
                $attachmentService = new \App\Services\AttachmentService();
                $attachmentService->uploadEmailAttachments($attachments, $ticket->id, $ticket->company_id, $user->id);
            }

            // Envoyer l'email de confirmation
            try {
                //\Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TicketConfirmationMail($ticket));
                Log::info("Confirmation email sent for Support n°{$ticket->id} to {$user->email}");
            } catch (Exception $e) {
                Log::error("Failed to send confirmation email for Support n°{$ticket->id}: " . $e->getMessage());
            }

            return $ticket;

        } catch (Exception $e) {
            Log::error("Error creating ticket from email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Déplace un email vers le dossier d'archives
     */
    /**
     * Déplace un email dans le dossier d'archives en utilisant son UID
     */
    private function moveEmailToArchiveByUid(int $uid): bool
    {
        try {
            // Créer le dossier d'archives si nécessaire
            if (!$this->ensureArchiveFolderExists()) {
                Log::error("Cannot create archive folder, marking email as read instead");
                imap_setflag_full($this->connection, $uid, "\\Seen", ST_UID);
                return true; // Continue le traitement
            }

            // Marquer comme lu avant de déplacer (en utilisant UID)
            imap_setflag_full($this->connection, $uid, "\\Seen", ST_UID);
            
            // Déplacer l'email en utilisant l'UID
            $result = imap_mail_move($this->connection, $uid, $this->archiveFolder, CP_UID);
            
            if ($result) {
                // Expunge pour finaliser le déplacement
                imap_expunge($this->connection);
                Log::info("Email UID {$uid} moved to {$this->archiveFolder}");
                return true;
            } else {
                Log::error("Failed to move email UID {$uid}: " . imap_last_error());
                Log::info("Email UID {$uid} was marked as read but could not be moved");
                return true; // Email traité et marqué comme lu même si pas déplacé
            }
        } catch (Exception $e) {
            Log::error("Error moving email UID {$uid} to archive: " . $e->getMessage());
            // Fallback: marquer comme lu
            imap_setflag_full($this->connection, $uid, "\\Seen", ST_UID);
            return true;
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

    /**
     * Extraire les pièces jointes d'un email (récursif pour gérer multipart imbriqués)
     */
    private function extractAttachments(int $emailId, object $structure, string $prefix = ''): array
    {
        $attachments = [];

        if (!isset($structure->parts) || !count($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $partNum => $part) {
            $currentPartNum = $prefix ? "{$prefix}." . ($partNum + 1) : ($partNum + 1);
            
            // Vérifier si c'est une pièce jointe (disposition = attachment ou inline avec filename)
            $isAttachment = false;
            $filename = null;

            // Vérifier la disposition
            if (isset($part->disposition)) {
                $disposition = strtolower($part->disposition);
                if ($disposition === 'attachment') {
                    $isAttachment = true;
                }
            }

            // Récupérer le nom du fichier
            if (isset($part->dparameters)) {
                foreach ($part->dparameters as $param) {
                    if (strtolower($param->attribute) === 'filename') {
                        $filename = $param->value;
                        $isAttachment = true;
                    }
                }
            }

            // Aussi vérifier dans parameters si pas trouvé
            if (!$filename && isset($part->parameters)) {
                foreach ($part->parameters as $param) {
                    if (strtolower($param->attribute) === 'name') {
                        $filename = $param->value;
                        if (!$isAttachment && isset($part->disposition)) {
                            $isAttachment = true;
                        }
                    }
                }
            }

            // Si c'est un multipart imbriqué, explorer récursivement
            if (isset($part->type) && $part->type === 1 && isset($part->parts)) {
                $nestedAttachments = $this->extractAttachmentsFromParts($emailId, $part->parts, $currentPartNum);
                $attachments = array_merge($attachments, $nestedAttachments);
            }
            
            // Si c'est une pièce jointe, l'extraire
            if ($isAttachment && $filename) {
                // Récupérer le contenu
                $data = imap_fetchbody($this->connection, $emailId, $currentPartNum);

                // Décoder selon l'encodage
                if ($part->encoding === 3) { // Base64
                    $data = base64_decode($data);
                } elseif ($part->encoding === 4) { // Quoted-printable
                    $data = quoted_printable_decode($data);
                }

                // Déterminer le type MIME
                $mimeType = 'application/octet-stream'; // Par défaut
                if (isset($part->type) && isset($part->subtype)) {
                    $mimeTypes = [
                        0 => 'text',
                        1 => 'multipart',
                        2 => 'message',
                        3 => 'application',
                        4 => 'audio',
                        5 => 'image',
                        6 => 'video',
                        7 => 'other'
                    ];
                    $typeStr = $mimeTypes[$part->type] ?? 'application';
                    $mimeType = $typeStr . '/' . strtolower($part->subtype);
                }

                $attachments[] = [
                    'filename' => $filename,
                    'data' => $data,
                    'size' => strlen($data),
                    'mime_type' => $mimeType
                ];

                Log::info("Extracted attachment: {$filename} ({$mimeType}, " . strlen($data) . " bytes)");
            }
        }

        return $attachments;
    }

    /**
     * Extraire les pièces jointes d'un tableau de parts (helper récursif)
     */
    private function extractAttachmentsFromParts(int $emailId, array $parts, string $prefix): array
    {
        $attachments = [];

        foreach ($parts as $partNum => $part) {
            $currentPartNum = "{$prefix}." . ($partNum + 1);

            $isAttachment = false;
            $filename = null;

            // Vérifier la disposition
            if (isset($part->disposition)) {
                $disposition = strtolower($part->disposition);
                if ($disposition === 'attachment') {
                    $isAttachment = true;
                }
            }

            // Récupérer le nom du fichier depuis dparameters
            if (isset($part->dparameters)) {
                foreach ($part->dparameters as $param) {
                    if (strtolower($param->attribute) === 'filename') {
                        $filename = $param->value;
                        $isAttachment = true;
                    }
                }
            }

            // Récupérer le nom du fichier depuis parameters
            if (!$filename && isset($part->parameters)) {
                foreach ($part->parameters as $param) {
                    if (strtolower($param->attribute) === 'name') {
                        $filename = $param->value;
                        if (!$isAttachment && isset($part->disposition)) {
                            $isAttachment = true;
                        }
                    }
                }
            }

            // Si c'est encore un multipart, continuer la récursion
            if (isset($part->type) && $part->type === 1 && isset($part->parts)) {
                $nestedAttachments = $this->extractAttachmentsFromParts($emailId, $part->parts, $currentPartNum);
                $attachments = array_merge($attachments, $nestedAttachments);
            }

            // Si c'est une pièce jointe, l'extraire
            if ($isAttachment && $filename) {
                $data = imap_fetchbody($this->connection, $emailId, $currentPartNum);

                // Décoder selon l'encodage
                if ($part->encoding === 3) { // Base64
                    $data = base64_decode($data);
                } elseif ($part->encoding === 4) { // Quoted-printable
                    $data = quoted_printable_decode($data);
                }

                // Déterminer le type MIME
                $mimeType = 'application/octet-stream';
                if (isset($part->type) && isset($part->subtype)) {
                    $mimeTypes = [
                        0 => 'text',
                        1 => 'multipart',
                        2 => 'message',
                        3 => 'application',
                        4 => 'audio',
                        5 => 'image',
                        6 => 'video',
                        7 => 'other'
                    ];
                    $typeStr = $mimeTypes[$part->type] ?? 'application';
                    $mimeType = $typeStr . '/' . strtolower($part->subtype);
                }

                $attachments[] = [
                    'filename' => $filename,
                    'data' => $data,
                    'size' => strlen($data),
                    'mime_type' => $mimeType
                ];

                Log::info("Extracted attachment: {$filename} ({$mimeType}, " . strlen($data) . " bytes)");
            }
        }

        return $attachments;
    }

}