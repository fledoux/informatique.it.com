<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketAttachmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','string','max:8'],
            'company_id' => ['required','integer'],
            'ticket_id' => ['required','integer'],
            'message_id' => ['nullable','integer'],
            'uploaded_by' => ['required','integer'],
            's3_path' => ['required','string','max:255'],
            'original_filename' => ['required','string','max:190'],
            'mime_type' => ['nullable','string','max:100'],
            'size_bytes' => ['nullable','integer']
        ];
    }
}