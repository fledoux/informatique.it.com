<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Services\EmailReplyCodeService;
use Illuminate\Console\Command;

class TestEmailReplyCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-reply-code {ticket_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test le système de codes de réponse email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test du système de codes de réponse email');
        
        // Récupérer un ticket existant ou le dernier créé
        $ticketId = $this->argument('ticket_id');
        
        if ($ticketId) {
            $ticket = Ticket::find($ticketId);
            if (!$ticket) {
                $this->error("❌ Support n°{$ticketId} introuvable");
                return 1;
            }
        } else {
            $ticket = Ticket::latest()->first();
            if (!$ticket) {
                $this->error("❌ Aucun ticket trouvé dans la base de données");
                return 1;
            }
        }
        
        $this->info("📧 Test avec le Support n°{$ticket->id} : {$ticket->subject}");
        
        // Test 1: Générer un code de réponse
        $this->info("\n1️⃣ Génération du code de réponse");
        $code = EmailReplyCodeService::generateReplyCode($ticket);
        $this->line("Code généré: {$code}");
        
        // Test 2: Formater le code pour email
        $this->info("\n2️⃣ Format pour email");
        $formattedCode = EmailReplyCodeService::formatCodeForEmail($code);
        $this->line("Code formaté: {$formattedCode}");
        
        // Test 3: Construire un email complet
        $this->info("\n3️⃣ Construction d'un email avec code");
        $sampleMessage = "Bonjour,\n\nVotre ticket a été créé avec succès.\nNous vous recontacterons bientôt.\n\nCordialement,\nL'équipe support";
        $emailWithCode = EmailReplyCodeService::buildEmailWithReplyCode($sampleMessage, $ticket, $code);
        $this->line("Email complet:");
        $this->line("─────────────────");
        $this->line($emailWithCode);
        $this->line("─────────────────");
        
        // Test 4: Extraction du code depuis l'email
        $this->info("\n4️⃣ Extraction du code depuis l'email");
        $extractedCode = EmailReplyCodeService::extractReplyCode($emailWithCode);
        $this->line("Code extrait: " . ($extractedCode ?? 'NON TROUVÉ'));
        
        // Test 5: Récupération du ticket depuis le code
        if ($extractedCode) {
            $this->info("\n5️⃣ Récupération du ticket depuis le code");
            $foundTicket = EmailReplyCodeService::getTicketFromReplyCode($extractedCode);
            if ($foundTicket) {
                $this->line("✅ Ticket trouvé: #{$foundTicket->id} - {$foundTicket->subject}");
            } else {
                $this->error("❌ Ticket non trouvé avec le code {$extractedCode}");
            }
        }
        
        // Test 6: Nettoyage du contenu email
        $this->info("\n6️⃣ Test de nettoyage d'une réponse");
        $replyContent = "Voici ma réponse au ticket.\n\nMerci pour votre aide.\n\n" . $formattedCode . "\n\nContenu original du ticket...";
        $cleanedContent = EmailReplyCodeService::cleanEmailContent($replyContent);
        $this->line("Contenu original:");
        $this->line($replyContent);
        $this->line("\nContenu nettoyé:");
        $this->line($cleanedContent);
        
        // Test 7: Validation des codes
        $this->info("\n7️⃣ Test de validation des codes");
        $validCodes = ['15.KD8BSBS35', '142.ABC123XYZ', '1.TESTCODE1', '999.ZZZZZZZZZ'];
        $invalidCodes = ['abc.123456789', '15.TOO_SHORT', '15.toolongcode', '15.abc123xyz', 'nopoint', '15.'];
        
        foreach ($validCodes as $testCode) {
            $isValid = EmailReplyCodeService::isValidReplyCode($testCode);
            $this->line("{$testCode}: " . ($isValid ? '✅ Valide' : '❌ Invalide'));
        }
        
        foreach ($invalidCodes as $testCode) {
            $isValid = EmailReplyCodeService::isValidReplyCode($testCode);
            $this->line("{$testCode}: " . ($isValid ? '✅ Valide' : '❌ Invalide'));
        }
        
        $this->info("\n🎉 Tests terminés avec succès!");
        return 0;
    }
}
