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
            subject: 'Support #' . $this->ticket->id . ' - Confirmation de votre demande',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Rendre le contenu de confirmation
        $confirmationContent = view('emails._support-confirmation', [
            'ticket' => $this->ticket
        ])->render();

        return new Content(
            view: 'emails.global',
            with: [
                'title' => 'Support #' . $this->ticket->id . ' - Confirmation de votre demande',
                'content' => $confirmationContent,
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
