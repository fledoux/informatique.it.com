<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','in:active,inactive'],
            'name' => ['required','string','max:190'],
            'siret' => ['nullable','string','max:20'],
            'vat_number' => ['nullable','string','max:32'],
            'email' => ['nullable','email','max:190'],
            'phone' => ['nullable','string','max:50'],
            'website' => ['nullable','url','max:190'],
            'address_line1' => ['nullable','string','max:190'],
            'address_line2' => ['nullable','string','max:190'],
            'zip' => ['nullable','string','max:20'],
            'city' => ['nullable','string','max:120'],
            'country' => ['nullable','string','size:2'],
            'notes' => ['nullable','string']
        ];
    }
}