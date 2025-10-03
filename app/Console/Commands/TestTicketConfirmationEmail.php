<?php

namespace App\Console\Commands;

use App\Mail\TicketConfirmationMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestTicketConfirmationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:test-confirmation {ticket_id : ID du ticket pour lequel envoyer la confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste l\'envoi d\'un email de confirmation de ticket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ticketId = $this->argument('ticket_id');
        
        try {
            $ticket = Ticket::with(['author', 'company'])->findOrFail($ticketId);
            
            $this->info("🎫 Ticket trouvé: #{$ticket->id} - {$ticket->subject}");
            $this->info("📧 Destinataire: {$ticket->author->email} ({$ticket->author->firstname} {$ticket->author->lastname})");
            
            if ($this->confirm('Envoyer l\'email de confirmation ?')) {
                Mail::to($ticket->author->email)->send(new TicketConfirmationMail($ticket));
                $this->info("✅ Email de confirmation envoyé avec succès !");
            } else {
                $this->info("❌ Envoi annulé.");
            }
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->error("❌ Ticket #{$ticketId} introuvable.");
            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'envoi: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
