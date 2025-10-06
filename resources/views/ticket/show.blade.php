@extends('layouts.app')

@section('title', __('global.Details') . ' ' . __('ticket.entity'))

@section('content')
    <div class="row">
        <div class="col-12 col-lg-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fa-regular fa-comments me-2"></i>
                        Conversation - Ticket #{{ $ticket->id }}
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Boutons de réponse --}}
                    @auth
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <a href="{{ route('ticket_message.create') }}?ticket_id={{ $ticket->id }}" class="btn btn-primary">
                                    <i class="fa-regular fa-reply me-2"></i>Répondre
                                </a>
                                @can('ticketmessage.create')
                                    @hasanyrole(['super-admin'])
                                        <a href="{{ route('ticket_message.create') }}?ticket_id={{ $ticket->id }}&internal=1" class="btn btn-warning ms-2">
                                            <i class="fa-solid fa-lock me-2"></i>Note interne
                                        </a>
                                    @endhasanyrole
                                @endcan
                            </div>
                        </div>
                    @endauth

                    {{-- Messages de conversation (plus récents en haut) --}}
                    @foreach($ticket->messages()->orderBy('created_at', 'desc')->get() as $message)
                        @if($message->status === 'active' || (auth()->user() && auth()->user()->hasRole('super-admin') && $message->status === 'internal'))
                            <div class="row mb-3">
                                <div class="col-2 text-end">
                                    <span class="fw-bold {{ $message->status === 'internal' ? 'text-warning' : ($message->author_id === $ticket->author_id ? 'text-secondary' : 'text-primary') }}">
                                        {{ $message->author->name ?? 'Support' }}
                                        @if($message->status === 'internal')
                                            <i class="fa-solid fa-lock text-warning ms-1" title="Message interne"></i>
                                        @endif
                                    </span><br>
                                    <small class="text-muted">
                                        {{ $message->created_at->format('d/m/Y') }}<br>{{ $message->created_at->format('H\hi') }}
                                    </small>
                                </div>
                                <div class="col-10">
                                    <div class="card border-2 {{ $message->status === 'internal' ? 'border-warning' : ($message->author_id === $ticket->author_id ? 'border-secondary' : 'border-primary') }}">
                                        <div class="card-body">
                                            @if($message->subject && $message->subject !== 'Re: ' . $ticket->subject)
                                                <strong>{{ $message->subject }}</strong><br>
                                            @endif
                                            {!! nl2br(e($message->body)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Message d'accueil --}}
                    <div class="row mb-4">
                        <div class="col-2 text-end text-primary">
                            <span class="fw-bold">Support</span><br>
                            <small class="text-muted">{{ $ticket->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="col-10">
                            <div class="card border-primary border-2">
                                <div class="card-body">
                                    <p class="mb-0">Nous avons bien reçu votre demande.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message client original (en bas pour le contexte) --}}
                    <div class="row">
                        <div class="col-2 text-end">
                            <span class="text-secondary fw-bold">{{ $ticket->author->name ?? 'Client' }}</span><br>
                            <small class="text-muted">
                                {{ $ticket->created_at->format('d/m/Y') }}<br>{{ $ticket->created_at->format('H\hi') }}
                            </small>
                        </div>
                        <div class="col-10">
                            <div class="card border-2 bg-light">
                                <div class="card-body">
                                    <strong>{{ $ticket->subject }}</strong><br>
                                    {!! nl2br(e($ticket->question)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar avec infos du ticket --}}
        <div class="col-12 col-lg-3">
            <div class="card border-secondary border-opacity-25">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h5 class="h6 mb-0">
                        <i class="fa-regular fa-ticket me-2"></i>Ticket #{{ $ticket->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">
                            {{ __('ticket.status.' . $ticket->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="badge {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">
                            {{ __('ticket.priorityFull.' . $ticket->priority) }}
                        </span>
                    </div>
                    
                    <p class="small text-muted">
                        Créé le {{ $ticket->created_at->format('d/m/Y à H\hi') }}
                    </p>

                    @if($ticket->company)
                        <div class="mb-3">
                            <strong class="h5">{{ $ticket->company->name }}</strong><br>
                            {{ $ticket->author->name ?? 'N/A' }}
                        </div>
                    @endif

                    @if($ticket->assigned_to)
                        <div class="mb-3">
                            <small class="text-muted">Assigné à :</small><br>
                            {{ $ticket->assignedTo->name ?? 'N/A' }}
                        </div>
                    @endif

                    <div class="text-success small">
                        <i class="fa-solid fa-square-check me-1"></i>
                        Conditions acceptées
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection