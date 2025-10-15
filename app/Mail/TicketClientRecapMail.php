<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketClientRecapMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Ticket $ticket,
        public TicketMessage $newMessage
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
            subject: 'Support n°' . $this->ticket->id . ' - Nouvelle réponse',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Récupérer tous les messages du ticket (du plus récent au plus ancien)
        $allMessages = $this->ticket->messages()
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Construire le contenu simple
        $content = '<strong>Sujet :</strong> ' . e($this->ticket->subject);

        $content .= '<hr style="margin: 20px 0; border: none; border-top: 1px solid #dee2e6;">';

        // Tous les messages (plus récents en premier)
        if ($allMessages->count() > 0) {
            $content .= '<p><strong>Messages (' . $allMessages->count() . ') :</strong></p>';

            foreach ($allMessages as $msg) {
                $isNew = $msg->id === $this->newMessage->id;
                $bgColor = $isNew ? '#fff3cd' : '#f8f9fa';
                
                // Si l'auteur est de la société 1 (équipe support), afficher "Support"
                $authorName = $msg->author->company_id === 1 ? 'Support' : e($msg->author->name);

                $content .= '<div style="background-color: ' . $bgColor . '; padding: 15px; margin-bottom: 10px; border-radius: 4px;">';
                if ($isNew) {
                    $content .= '<p style="margin: 0 0 10px 0; color: #856404;"><strong>NOUVEAU</strong></p>';
                }
                $content .= '<p style="margin: 0 0 5px 0;"><strong>' . $authorName . '</strong> - ' . $msg->created_at->format('d/m/Y à H\hi') . '</p>';
                if ($msg->subject) {
                    $content .= '<p style="margin: 0 0 10px 0;"><strong>' . e($msg->subject) . '</strong></p>';
                }
                $content .= '<p style="margin: 0;">' . $msg->body . '</p>';
                $content .= '</div>';
            }
        }

        // Question initiale (en dernier pour ordre anti-chronologique)
        $content .= '<hr style="margin: 20px 0; border: none; border-top: 1px solid #dee2e6;">';
        $content .= '<div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px;">';
        $content .= '<p style="margin: 0 0 10px 0;"><strong>Question initiale :</strong></p>';
        $content .= '<p style="margin: 0;">' . $this->ticket->question . '</p>';
        $content .= '</div>';

        $content .= '<p style="margin-top: 20px;" align="center"><a href="' . route('ticket.show', $this->ticket->id) . '" style="display: inline-block; background-color: #ff4c00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Voir la demande en ligne</a></p>';

        // Ajouter le code de réponse pour permettre au client de répondre par email
        $emailData = \App\Services\EmailReplyCodeService::buildEmailWithReplyCode(
            $content,
            $this->ticket,
            $this->ticket->author->email
        );

        return new Content(
            view: 'emails.global',
            with: [
                'title' => 'Support n°' . $this->ticket->id . ' - Nouvelle réponse',
                'content' => $emailData['content'],
                'header' => $emailData['header'],
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
