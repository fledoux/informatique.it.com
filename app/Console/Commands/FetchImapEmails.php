<?php

namespace App\Console\Commands;

use App\Services\ImapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchImapEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:fetch 
                            {--test : Test la connexion IMAP sans traiter les emails}
                            {--dry-run : Affiche les emails sans les traiter}';

    /**
     * The console command description.
     */
    protected $description = 'Récupère les emails IMAP et les convertit en tickets';

    /**
     * Execute the console command.
     */
    public function handle(ImapService $imapService): int
    {
        $this->info('🚀 Démarrage de la récupération des emails IMAP...');

        // Test de connexion uniquement
        if ($this->option('test')) {
            return $this->testConnection($imapService);
        }

        // Vérifier la configuration
        if (!$this->validateConfig()) {
            $this->error('❌ Configuration IMAP manquante ou incomplète');
            return self::FAILURE;
        }

        try {
            // Établir la connexion
            if (!$imapService->connect()) {
                $this->error('❌ Impossible de se connecter au serveur IMAP');
                return self::FAILURE;
            }

            $this->info('✅ Connexion IMAP établie avec succès');

            // Mode dry-run : afficher sans traiter
            if ($this->option('dry-run')) {
                return $this->dryRun($imapService);
            }

            // Récupérer et traiter les emails
            $processed = $imapService->fetchNewEmails();

            $imapService->disconnect();

            if ($processed > 0) {
                $this->info("✅ {$processed} email(s) traité(s) avec succès");
            } else {
                $this->info('ℹ️  Aucun nouvel email à traiter');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la récupération des emails: ' . $e->getMessage());
            Log::error('IMAP fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Test de connexion IMAP
     */
    private function testConnection(ImapService $imapService): int
    {
        $this->info('🔍 Test de connexion IMAP...');

        $result = $imapService->testConnection();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            
            if (!empty($result['details'])) {
                $this->table(['Statistique', 'Valeur'], [
                    ['Messages totaux', $result['details']['total_messages'] ?? 0],
                    ['Messages non lus', $result['details']['unread_messages'] ?? 0],
                    ['Messages récents', $result['details']['recent_messages'] ?? 0],
                ]);
            }
            
            return self::SUCCESS;
        } else {
            $this->error('❌ ' . $result['message']);
            return self::FAILURE;
        }
    }

    /**
     * Mode dry-run pour voir les emails sans les traiter
     */
    private function dryRun(ImapService $imapService): int
    {
        $this->info('🔍 Mode dry-run - Affichage des nouveaux emails sans traitement...');
        
        // Pour le dry-run, on aurait besoin d'une méthode spécifique dans ImapService
        // Pour l'instant, on affiche juste un message
        $this->warn('⚠️  Fonctionnalité dry-run à implémenter');
        
        $imapService->disconnect();
        return self::SUCCESS;
    }

    /**
     * Valide la configuration IMAP
     */
    private function validateConfig(): bool
    {
        $required = ['host', 'username', 'password'];
        
        foreach ($required as $key) {
            if (empty(config("imap.{$key}"))) {
                $this->error("❌ Configuration manquante: IMAP_{$key}");
                return false;
            }
        }

        return true;
    }

    /**
     * Affiche les instructions de configuration
     */
    private function showConfigInstructions(): void
    {
        $this->info('📋 Variables d\'environnement requises dans .env:');
        $this->line('');
        $this->line('IMAP_HOST=imap.gmail.com');
        $this->line('IMAP_PORT=993');
        $this->line('IMAP_USERNAME=your-email@gmail.com');
        $this->line('IMAP_PASSWORD=your-app-password');
        $this->line('IMAP_ENCRYPTION=ssl');
        $this->line('IMAP_FOLDER=INBOX');
        $this->line('');
        $this->warn('⚠️  Pour Gmail, utilisez un mot de passe d\'application, pas votre mot de passe principal');
    }
}