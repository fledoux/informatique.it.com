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

    public function getDateSlots(CarbonImmutable $date): array
    {
        $duration = (int) $this->slot_duration_minutes;

        // Seuls les créneaux enregistrés pour ce jour sont proposés
        $times = $this->manualSlots()
            ->whereDate('slot_date', $date->toDateString())
            ->orderBy('slot_time')
            ->pluck('slot_time')
            ->map(static fn ($value) => CarbonImmutable::parse($value)->format('H:i'))
            ->unique()
            ->values();

        $slots = [];

        foreach ($times as $time) {
            $slotStart = CarbonImmutable::parse($date->format('Y-m-d') . ' ' . $time);
            $slotEnd = $slotStart->addMinutes($duration);
            $slots[] = [
                'date' => $date->toDateString(),
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'key' => $date->toDateString() . '|' . $slotStart->format('H:i'),
                'starts_at' => $slotStart,
                'label' => $slotStart->format('H\hi') . ' à ' . $slotEnd->format('H\hi'),
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
