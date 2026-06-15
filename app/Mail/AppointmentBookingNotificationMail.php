<?php

namespace App\Mail;

use App\Models\AppointmentBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentBookingNotificationMail extends Mailable
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
            subject: 'Nouveau rendez-vous - ' . ($project->title ?? 'Prise de rendez-vous'),
        );
    }

    public function content(): Content
    {
        $booking = $this->booking;
        $project = $booking->project;

        $start = \Carbon\Carbon::parse($booking->starts_at);
        $end = \Carbon\Carbon::parse($booking->ends_at);

        $content = '<p>Un nouveau rendez-vous vient d\'être enregistré.</p>'
            . '<ul>'
            . '<li><strong>Projet :</strong> ' . e($project->title) . ' (' . e($project->serial) . ')</li>'
            . '<li><strong>Date :</strong> ' . e($start->locale('fr')->translatedFormat('l d F Y')) . '</li>'
            . '<li><strong>Créneau :</strong> ' . e($start->format('H:i')) . ' - ' . e($end->format('H:i')) . '</li>'
            . '<li><strong>Prénom :</strong> ' . e($booking->first_name) . '</li>'
            . '<li><strong>Nom :</strong> ' . e($booking->last_name) . '</li>'
            . '<li><strong>Email :</strong> ' . e($booking->email) . '</li>'
            . '<li><strong>Téléphone :</strong> ' . e($booking->phone ?? '-') . '</li>'
            . '</ul>';

        return new Content(
            view: 'emails.global',
            with: [
                'title' => 'Nouveau rendez-vous',
                'content' => $content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
