<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TicketConfirmationMail extends Mailable
{

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('app.company.emails.help'),
            subject: 'Support n°' . $this->ticket->id . ' - Confirmation de votre demande',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Générer le contenu de base
        $confirmationContent = view('emails._support-confirmation', [
            'ticket' => $this->ticket
        ])->render();
        
        // Ajouter le code de réponse
        $emailData = \App\Services\EmailReplyCodeService::buildEmailWithReplyCode(
            $confirmationContent, 
            $this->ticket
        );

        return new Content(
            view: 'emails.global',
            with: [
                'title' => 'Support n°' . $this->ticket->id . ' - Confirmation de votre demande',
                'content' => $emailData['content'],
                'header' => $emailData['header'],
                'ticket' => $this->ticket
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
