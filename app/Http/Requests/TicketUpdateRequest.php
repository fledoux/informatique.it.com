<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = Auth::user();
        $isAdminLevel = $user && $user->hasRole('super-admin');
        
        return [
            'status' => ['required','in:new,in_progress,waiting,resolved,closed,canceled'],
            'priority' => ['required','in:low,normal,high,urgent'],
            'company_id' => ['sometimes','integer'], // Toujours optionnel car géré automatiquement
            'author_id' => $isAdminLevel ? ['required','integer'] : ['sometimes','integer'],
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