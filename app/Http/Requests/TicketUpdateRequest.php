<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','in:new,in_progress,waiting,resolved,closed,canceled'],
            'priority' => ['required','in:low,normal,high,urgent'],
            'company_id' => ['required','integer'],
            'author_id' => ['required','integer'],
            'assigned_to' => ['nullable','integer'],
            'assigned_at' => ['nullable','date'],
            'due' => ['nullable','date'],
            'folder_code' => ['nullable','string','max:64'],
            'subject' => ['required','string','max:190'],
            'question' => ['nullable','string'],
            'billable' => ['required','boolean']
        ];
    }
}