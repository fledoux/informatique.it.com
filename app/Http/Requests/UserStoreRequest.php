<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required','string','max:255'],
            'password' => ['nullable','string','min:8'],
            'status' => ['required','in:active,inactive'],
            'firstname' => ['nullable','string','max:120'],
            'lastname' => ['nullable','string','max:120'],
            'phone' => ['nullable','string','max:50'],
            'last_login' => ['nullable','date'],
            'channels' => ['nullable','array','nullable'],
            'roles' => ['nullable','array'],
            'roles.*' => ['exists:roles,name']
        ];

        // Champs réservés aux super-admins
        if (Auth::user() && Auth::user()->hasRole('super-admin')) {
            $rules['email'] = ['required','email','max:255'];
            $rules['company_id'] = ['nullable','integer'];
            $rules['agree_terms'] = ['required','in:oui,non'];
            $rules['note'] = ['nullable','string','max:255'];
        }

        return $rules;
    }
}