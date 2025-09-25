<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketStoreRequest extends FormRequest
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
            'status' => $isAdminLevel ? ['required','in:new,in_progress,waiting,resolved,closed,canceled'] : ['sometimes','in:new,in_progress,waiting,resolved,closed,canceled'],
            'priority' => ['required','in:low,normal,high,urgent'],
            'company_id' => $isAdminLevel ? ['required','integer'] : ['sometimes','integer'],
            'author_id' => $isAdminLevel ? ['required','integer'] : ['sometimes','integer'],
            'assigned_to' => ['nullable','integer'],
            'assigned_at' => ['nullable','date'],
            'due' => ['nullable','date'],
            'folder_code' => ['nullable','string','max:64'],
            'subject' => ['required','string','max:190'],
            'question' => ['required','string'],
            'billable' => $isAdminLevel ? ['required','boolean'] : ['sometimes','boolean']
        ];
    }
}