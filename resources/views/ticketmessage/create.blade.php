@extends('layouts.app')

@section('title', ($isInternal ? 'Note interne' : 'Répondre') . ' - Ticket #' . $ticket->id)

@section('content')
    <div class="container-xxl py-4 px-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
            <h1 class="h3 mb-0">
                <i class="fa-regular fa-{{ $isInternal ? 'lock' : 'reply' }} me-2"></i>
                {{ $isInternal ? 'Note interne' : 'Répondre' }} - Ticket #{{ $ticket->id }}
            </h1>
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-secondary">
                    <i class="fa-regular fa-arrow-left me-2"></i>Retour au ticket
                </a>
            </div>
        </div>

        {{-- Formulaire de réponse --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa-regular fa-{{ $isInternal ? 'lock' : 'pencil' }} me-2"></i>
                    {{ $isInternal ? 'Nouvelle note interne' : 'Votre réponse' }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('ticketmessage.store') }}" method="POST">
                    @csrf

                    {{-- Champs cachés --}}
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                    <input type="hidden" name="status" value="{{ $isInternal ? 'internal' : 'active' }}">

                    {{-- Sujet --}}
                    <div class="mb-3">
                        <label for="subject" class="form-label">
                            Sujet <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" 
                               name="subject" 
                               value="{{ old('subject', '') }}" 
                               maxlength="190"
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ $isInternal ? 'Sujet de la note interne (pour organisation interne)' : 'Ce sujet sera utilisé pour les notifications email/SMS' }}
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="mb-3">
                        <label for="body" class="form-label">
                            {{ $isInternal ? 'Contenu de la note' : 'Votre message' }}
                        </label>
                        <textarea class="form-control @error('body') is-invalid @enderror" 
                                  id="body" 
                                  name="body" 
                                  rows="8"
                                  placeholder="{{ $isInternal ? 'Tapez votre note interne ici (visible seulement par l\'équipe)...' : 'Tapez votre réponse ici...' }}">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($isInternal)
                            <div class="alert alert-warning mt-2">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <strong>Note interne :</strong> Ce message ne sera visible que par l'équipe support et n'apparaîtra pas côté client.
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-secondary">
                            <i class="fa-regular fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-{{ $isInternal ? 'warning' : 'primary' }}">
                            <i class="fa-regular fa-{{ $isInternal ? 'lock' : 'paper-plane' }} me-2"></i>
                            {{ $isInternal ? 'Ajouter la note' : 'Envoyer la réponse' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection