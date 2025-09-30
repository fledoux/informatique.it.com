<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllowDomainRegistrationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required','integer'],
            'domain' => ['required','string','max:255']
        ];
    }
}