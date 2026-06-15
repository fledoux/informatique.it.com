<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_project_id',
        'appointment_date',
        'starts_at',
        'ends_at',
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(AppointmentProject::class, 'appointment_project_id');
    }
}
