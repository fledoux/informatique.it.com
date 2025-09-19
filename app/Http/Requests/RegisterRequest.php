<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults()
            ],
            'agree_terms' => [
                'required',
                'accepted'
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('Register.Email is required'),
            'email.email' => __('Register.Email must be valid'),
            'email.unique' => __('Register.Email already exists'),
            'password.required' => __('Register.Password is required'),
            'password.confirmed' => __('Register.Password confirmation does not match'),
            'agree_terms.required' => __('Register.You must accept the terms'),
            'agree_terms.accepted' => __('Register.You must accept the terms'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('Register.Email'),
            'password' => __('Register.Password'),
            'agree_terms' => __('Register.Agree terms'),
        ];
    }
}