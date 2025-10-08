<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketAttachmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSizeKb = config('attachments.max_size', 20971520) / 1024; // Convertir en Ko

        return [
            'ticket_id' => ['required','integer','exists:tickets,id'],
            'company_id' => ['required','integer','exists:companies,id'],
            'uploaded_by' => ['required','integer','exists:users,id'],
            'status' => ['required','string','in:active,inactive'],
            'files' => ['required','array','min:1'],
            'files.*' => ['required','file','max:' . $maxSizeKb],
        ];
    }
}