<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use App\Models\AppointmentBooking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AppointmentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial',
        'title',
        'subtitle',
        'notification_email',
        'single_registration_per_person',
        'slot_duration_minutes',
        'day_start_time',
        'day_end_time',
        'booking_horizon_days',
        'is_active',
        'show_booking_names',
    ];

    protected $casts = [
        'single_registration_per_person' => 'boolean',
        'slot_duration_minutes' => 'integer',
        'booking_horizon_days' => 'integer',
        'is_active' => 'boolean',
        'show_booking_names' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(AppointmentBooking::class);
    }

    public function manualSlots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class);
    }

    public function getPublicUrl(): string
    {
        return route('appointment.public.show', ['serial' => $this->serial]);
    }

    public function getEffectiveBookingHorizonDays(): int
    {
        if (is_null($this->booking_horizon_days)) {
            return 30;
        }

        return max(1, (int) $this->booking_horizon_days);
    }

    public function getMaxBookingDate(): CarbonImmutable
    {
        $horizonDays = $this->getEffectiveBookingHorizonDays();
        $i = 0;
        $daysDisplayed = 0;
        $maxIterations = 365; // Limite de sécurité : chercher max 1 an de jours

        while ($daysDisplayed < $horizonDays && $i < $maxIterations) {
            $date = CarbonImmutable::today()->addDays($i);
            $slots = $this->getDateSlots($date);
            if (count($slots) > 0) {
                $daysDisplayed++;
                if ($daysDisplayed === $horizonDays) {
                    return $date->endOfDay();
                }
            }
            $i++;
        }

        return CarbonImmutable::today()->addDays($horizonDays - 1)->endOfDay();
    }

    public function getDateSlots(CarbonImmutable $date): array
    {
        $duration = (int) $this->slot_duration_minutes;

        // Récupérer tous les créneaux du jour avec leurs détails
        $manualSlots = $this->manualSlots()
            ->whereDate('slot_date', $date->toDateString())
            ->orderBy('slot_time')
            ->get();

        $slots = [];

        foreach ($manualSlots as $slot) {
            $slotStart = CarbonImmutable::parse($date->format('Y-m-d') . ' ' . $slot->slot_time);
            $slotEnd = $slotStart->addMinutes($duration);
            
            // Générer le label avec le temps d'début et fin
            $label = $slotStart->format('H\hi') . ' à ' . $slotEnd->format('H\hi');
            // Si c'est une pause avec texte, afficher en format multi-ligne
            if ($slot->break_text) {
                $label = $slotStart->format('H\hi') . "\n" . $slot->break_text;
            }
            
            $slots[] = [
                'date' => $date->toDateString(),
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'key' => $date->toDateString() . '|' . $slotStart->format('H:i'),
                'starts_at' => $slotStart,
                'label' => $label,
                'break_text' => $slot->break_text,
                'is_break' => !is_null($slot->break_text),
            ];
        }

        return $slots;
    }

    /**
     * Génére la liste des heures (H:i) entre l'horaire de début et de fin
     * selon la durée du créneau. Sert à pré-remplir une journée.
     *
     * @return array<int, string>
     */
    public function generateTimesForDay(int $breakTimeMinutes = 0): array
    {
        $duration = (int) $this->slot_duration_minutes;
        $date = CarbonImmutable::today();
        $start = CarbonImmutable::parse($date->format('Y-m-d') . ' ' . $this->day_start_time);
        $end = CarbonImmutable::parse($date->format('Y-m-d') . ' ' . $this->day_end_time);

        $times = [];
        $cursor = $start;

        while ($cursor->addMinutes($duration)->lessThanOrEqualTo($end)) {
            $times[] = $cursor->format('H:i');
            $cursor = $cursor->addMinutes($duration + $breakTimeMinutes);
        }

        return $times;
    }
}
