<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnauthorizedReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderEmail,
        public string $ticketId
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('app.company.emails.help'),
            subject: 'Accès refusé - Inscription requise',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unauthorized-reply',
            with: [
                'senderEmail' => $this->senderEmail,
                'ticketId' => $this->ticketId,
                'registerUrl' => route('register'),
                'loginUrl' => route('login'),
                'title' => 'Accès refusé - Inscription requise',
                'content' => 'Vous avez tenté d\'envoyer une demande au Support.<br>Veuillez vous inscrire pour utiliser ce service à l\'adresse : https://informatique-it.com/inscription.',
            ],
        );
    }
}