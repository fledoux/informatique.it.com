<?php

namespace App\Mail;

use App\Models\AppointmentBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentBookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AppointmentBooking $booking
    ) {
        //
    }

    public function envelope(): Envelope
    {
        $project = $this->booking->project;

        return new Envelope(
            from: config('app.company.emails.help'),
            subject: 'Confirmation de votre rendez-vous - ' . ($project->title ?? 'Prise de rendez-vous'),
        );
    }

    public function content(): Content
    {
        $booking = $this->booking;
        $project = $booking->project;

        $start = \Carbon\Carbon::parse($booking->starts_at);
        $end = \Carbon\Carbon::parse($booking->ends_at);

        $content = '<p>Bonjour ' . e($booking->first_name) . ' ' . e($booking->last_name) . ',</p>'
            . '<p>Votre rendez-vous a bien été enregistré.</p>'
            . '<ul>'
            . '<li><strong>Objet :</strong> ' . e($project->title) . '</li>'
            . '<li><strong>Date :</strong> ' . e($start->locale('fr')->translatedFormat('l d F Y')) . '</li>'
            . '<li><strong>Horaire :</strong> ' . e($start->format('H:i')) . ' - ' . e($end->format('H:i')) . '</li>'
            . '</ul>'
            . '<p>Si vous avez besoin de modifier ou annuler ce rendez-vous, merci de nous contacter.</p>'
            . '<p>À bientôt.</p>';

        return new Content(
            view: 'emails.global',
            with: [
                'title' => 'Confirmation de votre rendez-vous',
                'content' => $content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
