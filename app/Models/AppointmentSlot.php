<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_project_id',
        'slot_date',
        'slot_time',
        'break_text',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(AppointmentProject::class, 'appointment_project_id');
    }
}
