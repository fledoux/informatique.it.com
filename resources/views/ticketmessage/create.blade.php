@extends('layouts.app')

@section('title', ($isInternal ? 'Note interne' : 'Répondre') . ' - Support n°' . $ticket->id)

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
            <h1 class="h3 mb-0">
                {!! $isInternal ? __('ticketmessage.h1.InternalNote') : __('ticketmessage.h1.Reply') !!} - Support n°{{ $ticket->id }}
            </h1>
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-secondary">
                    <i class="fa-regular fa-arrow-left me-2"></i>Retour au ticket
                </a>
            </div>
        </div>

        {{-- Formulaire de réponse --}}
        <div class="card">
            <div class="card-body p-2 p-sm-3">
                <form action="{{ route('ticketmessage.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Champs cachés --}}
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                    <input type="hidden" name="status" value="{{ $isInternal ? 'internal' : 'active' }}">

                    {{-- Sujet --}}
                    <div class="mb-3">
                        <label for="subject" class="form-label">
                            Sujet <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                            name="subject" value="{{ old('subject', '') }}" maxlength="190" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ $isInternal ? 'Sujet de la note interne (pour organisation interne)' : 'Ce sujet sera utilisé pour les notifications email/SMS' }}
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="mb-5">
                        <label for="body" class="form-label">
                            {{ $isInternal ? 'Contenu de la note' : 'Votre message' }}
                        </label>
                        <textarea class="form-control tinymce @error('body') is-invalid @enderror" id="body" name="body" rows="8"
                            placeholder="{{ $isInternal ? 'Tapez votre note interne ici (visible seulement par l\'équipe)...' : 'Tapez votre réponse ici...' }}">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($isInternal)
                            <div class="alert alert-warning mt-2">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <strong>Note interne :</strong> Ce message ne sera visible que par l'équipe support et
                                n'apparaîtra pas côté client.
                            </div>
                        @endif
                    </div>

                    {{-- Pièces jointes --}}
                    <div class="mb-5">
                        <label for="files" class="form-label">
                            <i class="fa-regular fa-paperclip me-2"></i>{{ __('ticketattachment.fields.files') }}
                        </label>
                        <input type="file" class="form-control @error('files') is-invalid @enderror" id="files"
                            name="files[]" multiple>
                        @error('files')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('files.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fa-regular fa-info-circle me-1"></i>
                            Taille maximale :
                            <strong>{{ \App\Helpers\Helper::getMaxFileSizeFormatted() }}</strong>.
                            Vous pouvez sélectionner plusieurs fichiers.
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="btn-group mt-3">
                        <button type="submit" class="btn btn-{{ $isInternal ? 'warning' : 'primary' }}">
                            <i class="fa-regular fa-{{ $isInternal ? 'lock' : 'paper-plane' }} me-2"></i>
                            {{ $isInternal ? 'Ajouter la note' : 'Envoyer la réponse' }}
                        </button>
                        <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-primary">
                            <i class="fa-regular fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
@endsection
