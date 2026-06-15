<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('appointment_project');

        return [
            'serial' => ['required', 'string', 'max:36', Rule::unique('appointment_projects', 'serial')->ignore($projectId)],
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
