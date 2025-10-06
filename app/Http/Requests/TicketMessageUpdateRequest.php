<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketMessageUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','string','max:8'],
            'subject' => ['required','string','max:190'],
            'body' => ['nullable','string'],
            'company_id' => ['required','integer'],
            'ticket_id' => ['required','integer'],
            'author_id' => ['required','integer']
        ];
    }
}