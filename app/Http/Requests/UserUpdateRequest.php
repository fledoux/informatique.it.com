<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
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
            'firstname' => ['required','string','max:120'],
            'lastname' => ['required','string','max:120'],
            'phone' => ['nullable','string','max:50'],
            'last_login' => ['nullable','date'],
            'channels' => ['nullable','array','nullable']
        ];

        // Champs réservés aux super-admins
        if (Auth::user() && Auth::user()->hasRole('super-admin')) {
            $rules['email'] = ['required','email','max:255'];
            $rules['company_id'] = ['required','integer'];
            $rules['agree_terms'] = ['required','in:oui,non'];
            $rules['roles'] = ['required','array'];
            $rules['roles.*'] = ['exists:roles,name'];
            $rules['note'] = ['nullable','string','max:255'];
        } else {
            // Pour les managers/admins, roles est optionnel (ils ne peuvent pas le modifier)
            $rules['roles'] = ['nullable','array'];
            $rules['roles.*'] = ['exists:roles,name'];
        }

        return $rules;
    }
}