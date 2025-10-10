@extends('layouts.app')

@section('title', 'Fusionner les tickets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fa-regular fa-code-merge me-2"></i>
            Fusionner les tickets
        </h1>
        <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-secondary">
            <i class="fa-regular fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="alert alert-info">
        <i class="fa-regular fa-circle-info me-2"></i>
        <strong>Information :</strong> Cette opération va fusionner un autre ticket avec le ticket actuel.
        Tous les messages et pièces jointes de l'ancien ticket seront transférés vers ce ticket, puis l'ancien ticket sera supprimé.
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa-regular fa-check me-2"></i>
                        Ticket à conserver
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Ticket n°</dt>
                        <dd class="col-sm-8"><strong>#{{ $ticket->id }}</strong></dd>

                        <dt class="col-sm-4">Sujet</dt>
                        <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                        <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ \App\Helpers\Helper::getStatusBadgeColor($ticket->status) }}">
                                {{ __('ticket.status.' . $ticket->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Priorité</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ \App\Helpers\Helper::getPriorityBadgeColor($ticket->priority) }}">
                                {{ __('ticket.priority.' . $ticket->priority) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Créé le</dt>
                        <dd class="col-sm-8">{{ $ticket->created_at->format('d/m/y à H\hi') }}</dd>

                        <dt class="col-sm-4">Auteur</dt>
                        <dd class="col-sm-8">{{ $ticket->author->firstname ?? '' }} {{ $ticket->author->lastname ?? '' }}</dd>

                        <dt class="col-sm-4">Messages</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary">{{ $ticket->messages_count }}</span>
                        </dd>

                        <dt class="col-sm-4">Pièces jointes</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-secondary">{{ $ticket->attachments_count }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <form method="POST" action="{{ route('ticket.merge', $ticket->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir fusionner ces tickets ? Cette action est irréversible.')">
                @csrf
                
                <div class="card border-warning">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">
                            <i class="fa-regular fa-triangle-exclamation me-2"></i>
                            Ticket à fusionner (sera supprimé)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($availableTickets->isEmpty())
                            <div class="alert alert-warning mb-0">
                                <i class="fa-regular fa-circle-info me-2"></i>
                                Aucun ticket disponible pour la fusion.
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="source_ticket_id" class="form-label">
                                    Sélectionner le ticket à fusionner <span class="text-danger">*</span>
                                </label>
                                <select name="source_ticket_id" id="source_ticket_id" class="form-select" required onchange="updateTicketInfo(this)">
                                    <option value="">-- Choisir un ticket --</option>
                                    @foreach($availableTickets as $availableTicket)
                                        <option value="{{ $availableTicket->id }}" 
                                                data-subject="{{ $availableTicket->subject }}"
                                                data-status="{{ $availableTicket->status }}"
                                                data-priority="{{ $availableTicket->priority }}"
                                                data-created="{{ $availableTicket->created_at->format('d/m/y à H\hi') }}"
                                                data-author="{{ $availableTicket->author->firstname ?? '' }} {{ $availableTicket->author->lastname ?? '' }}"
                                                data-messages="{{ $availableTicket->messages_count }}"
                                                data-attachments="{{ $availableTicket->attachments_count }}">
                                            #{{ $availableTicket->id }} - {{ \Illuminate\Support\Str::limit($availableTicket->subject, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_ticket_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="ticket-preview" class="d-none">
                                <hr>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Ticket n°</dt>
                                    <dd class="col-sm-8"><strong id="preview-id"></strong></dd>

                                    <dt class="col-sm-4">Sujet</dt>
                                    <dd class="col-sm-8" id="preview-subject"></dd>

                                    <dt class="col-sm-4">Statut</dt>
                                    <dd class="col-sm-8" id="preview-status"></dd>

                                    <dt class="col-sm-4">Priorité</dt>
                                    <dd class="col-sm-8" id="preview-priority"></dd>

                                    <dt class="col-sm-4">Créé le</dt>
                                    <dd class="col-sm-8" id="preview-created"></dd>

                                    <dt class="col-sm-4">Auteur</dt>
                                    <dd class="col-sm-8" id="preview-author"></dd>

                                    <dt class="col-sm-4">Messages</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge bg-secondary" id="preview-messages"></span>
                                    </dd>

                                    <dt class="col-sm-4">Pièces jointes</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge bg-secondary" id="preview-attachments"></span>
                                    </dd>
                                </dl>
                            </div>
                        @endif
                    </div>
                    @if(!$availableTickets->isEmpty())
                        <div class="card-footer">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa-regular fa-code-merge me-2"></i>
                                Fusionner les tickets
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-warning">
        <h5 class="alert-heading">
            <i class="fa-regular fa-triangle-exclamation me-2"></i>
            Attention
        </h5>
        <ul class="mb-0">
            <li>Tous les <strong>messages</strong> du ticket à fusionner seront transférés vers le ticket à conserver</li>
            <li>Toutes les <strong>pièces jointes</strong> seront également transférées</li>
            <li>Les <strong>dates de création</strong> des messages et pièces jointes seront conservées</li>
            <li>Un <strong>message système</strong> sera créé pour indiquer la fusion</li>
            <li>Le ticket à fusionner sera <strong>définitivement supprimé</strong></li>
            <li>Cette action est <strong>irréversible</strong></li>
        </ul>
    </div>
@endsection

@push('javascripts')
<script>
function updateTicketInfo(select) {
    const option = select.options[select.selectedIndex];
    const preview = document.getElementById('ticket-preview');
    
    if (option.value) {
        document.getElementById('preview-id').textContent = '#' + option.value;
        document.getElementById('preview-subject').textContent = option.dataset.subject;
        document.getElementById('preview-status').innerHTML = '<span class="badge bg-secondary">' + option.dataset.status + '</span>';
        document.getElementById('preview-priority').innerHTML = '<span class="badge bg-secondary">' + option.dataset.priority + '</span>';
        document.getElementById('preview-created').textContent = option.dataset.created;
        document.getElementById('preview-author').textContent = option.dataset.author;
        document.getElementById('preview-messages').textContent = option.dataset.messages;
        document.getElementById('preview-attachments').textContent = option.dataset.attachments;
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
}
</script>
@endpush
