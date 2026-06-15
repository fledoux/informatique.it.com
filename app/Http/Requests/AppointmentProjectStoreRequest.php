<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentProjectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'serial' => ['required', 'string', 'max:36', 'unique:appointment_projects,serial'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45'],
            'day_start_time' => ['required', 'date_format:H:i'],
            'day_end_time' => ['required', 'date_format:H:i', 'after:day_start_time'],
            'booking_horizon_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'single_registration_per_person' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
