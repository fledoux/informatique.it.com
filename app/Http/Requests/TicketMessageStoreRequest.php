<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // On gère l'autorisation au niveau du controller
    }

    public function rules(): array
    {
        $maxFileSize = config('attachments.max_size', 20971520) / 1024; // Convert to KB
        
        return [
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'subject' => ['required', 'string', 'max:190'],
            'body' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive,internal'],
            'send_email_to_client' => ['nullable', 'boolean'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:' . $maxFileSize],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Le ticket est requis.',
            'ticket_id.exists' => 'Le ticket sélectionné n\'existe pas.',
            'subject.required' => 'Le sujet est requis.',
            'subject.max' => 'Le sujet ne peut pas dépasser :max caractères.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être : active, inactive ou internal.',
            'files.array' => 'Les fichiers doivent être un tableau.',
            'files.*.file' => 'Chaque élément doit être un fichier valide.',
            'files.*.max' => 'Chaque fichier ne doit pas dépasser :max Ko.',
        ];
    }
}