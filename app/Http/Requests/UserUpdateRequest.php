<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'password' => ['nullable','string','min:8'],
            'status' => ['required','in:active,inactive'],
            'company_id' => ['required','integer'],
            'firstname' => ['required','string','max:120'],
            'lastname' => ['required','string','max:120'],
            'phone' => ['nullable','string','max:50'],
            'last_login' => ['nullable','date'],
            'agree_terms' => ['required','in:oui,non'],
            'channels' => ['nullable','array','nullable'],
            'note' => ['nullable','string','max:255'],
            'roles' => ['required','array'],
            'roles.*' => ['exists:roles,name']
        ];
    }
}