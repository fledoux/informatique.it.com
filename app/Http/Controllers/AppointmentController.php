<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentBookingStoreRequest;
use App\Models\AppointmentBooking;
use App\Models\AppointmentProject;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;

class AppointmentController extends Controller
{
    public function show(string $serial)
    {
        $project = AppointmentProject::query()
            ->where('serial', $serial)
            ->where('is_active', true)
            ->firstOrFail();

        $startDate = CarbonImmutable::today();
        $horizonDays = $project->getEffectiveBookingHorizonDays();
        $dates = collect();
        $daysDisplayed = 0;
        $i = 0;

        while ($daysDisplayed < $horizonDays) {
            $date = $startDate->addDays($i);
            $slots = $project->getDateSlots($date);

            // Sauter les jours sans créneaux
            if (count($slots) === 0) {
                $i++;
                continue;
            }

            $bookings = AppointmentBooking::query()
                ->where('appointment_project_id', $project->id)
                ->whereDate('appointment_date', $date->toDateString())
                ->get()
                ->keyBy(static fn ($booking) => $booking->starts_at->format('Y-m-d H:i'));

            $dateSlots = collect($slots)
                ->map(static function (array $slot) use ($bookings) {
                    $slotKey = $slot['starts_at']->format('Y-m-d H:i');
                    $booking = $bookings[$slotKey] ?? null;
                    $slot['is_available'] = is_null($booking);
                    $slot['booking_name'] = $booking ? $booking->first_name . ' ' . $booking->last_name : null;
                    return $slot;
                })
                ->values()
                ->all();

            $dates->push([
                'date' => $date,
                'slots' => $dateSlots,
            ]);

            $daysDisplayed++;
            $i++;
        }

        return view('appointment.public', compact('project', 'dates'));
    }

    public function store(AppointmentBookingStoreRequest $request, string $serial)
    {
        $project = AppointmentProject::query()
            ->where('serial', $serial)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validated();
        [$appointmentDate, $slotStart] = explode('|', $validated['selected_slot']);

        $duration = (int) $project->slot_duration_minutes;
        if (!in_array($duration, [15, 30, 45], true)) {
            return back()->withInput()->withErrors([
                'selected_slot' => 'Configuration du projet invalide (durée de créneau).',
            ]);
        }

        $start = CarbonImmutable::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $slotStart);
        $end = $start->addMinutes($duration);

        if ($start->isBefore(CarbonImmutable::today()->startOfDay())) {
            return back()->withInput()->withErrors([
                'selected_slot' => 'La date choisie doit être aujourd\'hui ou ultérieure.',
            ]);
        }

        if (!is_null($project->booking_horizon_days)) {
            $maxDate = $project->getMaxBookingDate();
            if ($start->isAfter($maxDate)) {
                return back()->withInput()->withErrors([
                    'selected_slot' => 'La date dépasse la fenêtre de réservation autorisée pour ce projet.',
                ]);
            }
        }

        $expectedSlots = collect($project->getDateSlots(CarbonImmutable::parse($appointmentDate)));
        $isSlotAllowed = $expectedSlots->contains(static fn (array $slot) => $slot['key'] === $validated['selected_slot']);

        if (!$isSlotAllowed) {
            return back()->withInput()->withErrors([
                'selected_slot' => 'Le créneau choisi n\'est pas disponible pour ce projet.',
            ]);
        }

        if ($project->single_registration_per_person) {
            $alreadyBooked = AppointmentBooking::query()
                ->where('appointment_project_id', $project->id)
                ->where('email', $validated['email'])
                ->exists();

            if ($alreadyBooked) {
                return back()->withInput()->withErrors([
                    'email' => 'Une réservation existe déjà avec cet email pour ce projet.',
                ]);
            }
        }

        // Vérifier que le créneau n'a pas été réservé entre-temps
        $conflictingBooking = AppointmentBooking::query()
            ->where('appointment_project_id', $project->id)
            ->where('starts_at', $start)
            ->exists();

        if ($conflictingBooking) {
            return back()->withInput()->withErrors([
                'selected_slot' => 'Ce créneau vient d\'être réservé. Merci d\'en choisir un autre.',
            ]);
        }

        try {
            $booking = AppointmentBooking::create([
                'appointment_project_id' => $project->id,
                'appointment_date' => $appointmentDate,
                'starts_at' => $start,
                'ends_at' => $end,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        } catch (QueryException) {
            return back()->withInput()->withErrors([
                'selected_slot' => 'Ce créneau vient d\'être réservé. Merci d\'en choisir un autre.',
            ]);
        }

        // Notification email a l'admin (email du projet ou email support par defaut)
        $notificationEmail = $project->notification_email ?: config('app.company.emails.help');

        if ($notificationEmail) {
            try {
                $booking->setRelation('project', $project);
                \Illuminate\Support\Facades\Mail::to($notificationEmail)
                    ->send(new \App\Mail\AppointmentBookingNotificationMail($booking));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Échec envoi email rendez-vous: ' . $e->getMessage());
            }
        }

        // Email de confirmation au client
        try {
            $booking->setRelation('project', $project);
            \Illuminate\Support\Facades\Mail::to($booking->email)
                ->send(new \App\Mail\AppointmentBookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Échec envoi email confirmation client: ' . $e->getMessage());
        }

        return redirect()
            ->route('appointment.public.show', ['serial' => $project->serial])
            ->with('success', 'Votre rendez-vous a bien été enregistré.');
    }
}
