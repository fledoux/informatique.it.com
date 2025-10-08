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
                            <div class="col-12 ">
                                <a href="{{ route('ticketmessage.create', [$ticket->id]) }}"
                                    class="btn btn-primary">
                                    <i class="fa-regular fa-reply me-2"></i>Répondre
                                </a>
                                @can('ticketmessage.create')
                                    @hasanyrole(['super-admin'])
                                        <a href="{{ route('ticketmessage.create', [$ticket->id, 'internal']) }}"
                                            class="btn btn-warning ms-2">
                                            <i class="fa-regular fa-lock me-2"></i>Note interne
                                        </a>
                                    @endhasanyrole
                                @endcan
                            </div>
                        </div>
                    @endauth

                    {{-- Messages de conversation (plus récents en haut) --}}
                    @foreach ($ticket->messages()->orderBy('created_at', 'desc')->get() as $message)
                        @if (
                            $message->status === 'active' ||
                                (auth()->user() &&
                                    auth()->user()->hasRole('super-admin') &&
                                    ($message->status === 'internal' || $message->status === 'inactive')))
                            <div class="row mb-3 {{ $message->status === 'inactive' ? ' opacity-25' : '' }}">
                                <div class="col-2 text-end">
                                    <span
                                        class="{{ $message->status === 'internal' ? 'text-warning' : ($message->status === 'inactive' ? 'text-muted' : ($message->author_id === $ticket->author_id ? 'text-secondary' : 'text-primary')) }}">
                                        @if ($message->status === 'internal')
                                            <i class="fa-regular fa-lock text-warning ms-1" title="Message interne"></i>
                                        @elseif ($message->status === 'inactive')
                                            <i class="fa-regular fa-eye-slash ms-1" title="Message inactif"></i>
                                        @endif
                                        @hasrole('super-admin')
                                            {{ $message->author->name ?? 'Support' }}
                                        @else
                                            Support
                                        @endhasrole
                                        <br>
                                        <small class="text-muted">
                                            {{ $message->created_at->format('d/m/Y') }} -
                                            {{ $message->created_at->format('H\hi') }}
                                        </small>
                                    </span>
                                </div>
                                <div class="col-10">
                                    <div
                                        class="card border-2 {{ $message->status === 'internal' ? 'border-warning bg-warning bg-opacity-25' : ($message->author_id === $ticket->author_id ? 'bg-light' : 'border-primary text-primary') }}">
                                        <div class="card-body">
                                            @hasrole('super-admin')
                                                <div class="float-end">
                                                    <a href="{{ route('ticketmessage.edit', $message) }}"
                                                        class="btn btn-link m-0 p-0" title="Modifier">
                                                        <i class="fa-regular fa-edit"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('ticketmessage.destroy', $message) }}"
                                                        class="d-inline" onsubmit="return confirm('Supprimer ce message ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger m-0 p-0"
                                                            title="Supprimer">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endhasrole
                                            @hasrole('toto')
                                                #{{ $message->id }}
                                            @endhasrole
                                            @if ($message->subject && $message->subject !== 'Re: ' . $ticket->subject)
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
                            <span class="">Support</span><br>
                            <small class="text-muted">{{ $ticket->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="col-10">
                            <div class="card border-primary text-primary border-2">
                                <div class="card-body">
                                    <p class="mb-0">Nous avons bien reçu votre demande.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message client original (en bas pour le contexte) --}}
                    <div class="row">
                        <div class="col-2 text-end">
                            <span class="text-secondary">{{ $ticket->author->name ?? 'Client' }}</span><br>
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
            <div class="card border border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-message-question"></i> Demande N°{{ $ticket->id }}</h3>
                </div>
                <div class="card-body">
                    <span
                        class="badge mb-3 {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">{{ __('ticket.status.' . $ticket->status) }}
                    </span>
                    <span
                        class="badge mb-3 {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">{{ __('ticket.priorityFull.' . $ticket->priority) }}
                    </span>
                    <p>
                        <small class="">
                            {{ \App\Helpers\Helper::formatDateWithFrenchDay($ticket->created_at, 'l, j F Y à H\hi', true) }}.
                        </small>
                    </p>
                    <p>
                        <i class="fa-regular fa-square-check text-success"></i> {!! __('ticket.yes') !!},
                        {{ $user->name ?? '' }}
                        {!! __('ticket.cgv') !!}
                    </p>
                    <p>
                        <span class="h4">
                            {{ $company->name ?? '' }}
                        </span><br>
                        {{ $user->name ?? '' }}<br>
                        @role('super-admin')
                            <small class="text-muted">{{ $ticket->author?->getRoleNames()->implode(', ') }}</small><br>
                        @endrole
                        {!! \App\Helpers\Helper::mailTo($user->email) !!}<br>
                        {!! $user->phone
                            ? '<a href="tel:' . $user->phone . '" class="text-orange text-decoration-none">' . $user->phone . '</a>'
                            : '' !!}
                    </p>
                    <p>
                        @if ($ticket->folder_code)
                            <span class="text-muted">{{ __('ticket.fields.folder_code') }}
                                : </span>{{ $ticket->folder_code ?? '' }}<br>
                        @endif
                        @if ($ticket->due)
                            <span class="text-muted">{{ __('ticket.fields.due') }} :</span>
                            <span>{{ \App\Helpers\Helper::formatDateWithFrenchDay($ticket->due, 'l j/m/y - H\hi', true) }}</span><br>
                        @endif
                        @if ($ticket->assignedTo?->name)
                            <span class="text-muted">
                                {{ __('ticket.fields.assigned_to') }} :
                            </span>
                            {{ $ticket->assignedTo?->name ?? '' }}<br>
                        @endif
                        @if ($ticket->assigned_at)
                            <span class="text-muted">
                                {{ __('ticket.fields.assigned_at') }} :
                            </span>
                            {{ $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('d/m/y à H\hi') : $ticket->assigned_at) : '' }}<br>
                        @endif
                        <span class="{{ $ticket->billable ? 'text-muted' : 'text-danger' }}">
                            {{ __('ticket.fields.billable') }} :
                        </span>
                        {!! $ticket->billable ? __('ticket.billable.yes') : __('ticket.billable.no') !!}
                    </p>
                    <div class="d-none d-sm-flex mt-3">
                        @include('ticket._addTicket')
                    </div>
                </div>
            </div>

            {{-- Facturation --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-money-bill-1"></i> Facturation</h3>
                </div>
                <div class="card-body pb-0">
                    <p>
                        Estimation : <span class="float-end">2 tickets</span><br>
                        Facturation : <span class="float-end">1 ticket</span><br>
                        Majoration : <span class="float-end">0 ticket</span>
                    </p>
                </div>
                <div class="card-footer bg-secondary-subtle">
                    <span class="text-secondary">Solde au {{ now()->format('d/m/y') }} : <span class="float-end">12
                            {{ abs(12) > 1 ? __('ticket.tickets') : __('ticket.ticket') }}</span></span><br>
                </div>
            </div>
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-file-import"></i> Fichiers joints</h3>
                </div>
                <div class="card-body pb-0">
                    <p claass="p-0">ici2</p>
                </div>
            </div>

            {{-- Approbation --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-thumbs-up"></i> Demande d'Approbation</h3>
                </div>
                <div class="card-body pb-0">
                    @php
                        $managers = $ticket->getManagers();
                    @endphp

                    @if ($managers->count() > 0)
                        Voici la liste des approbateurs actuels, que nous pouvons solliciter uniquement pour vos demandes
                        liées à la sécurité :
                        </p>
                        <ul class="small fa-ul">
                            @foreach ($managers as $manager)
                                <li class="mb-2">
                                    <span class="fa-li"><i class="fa-regular fa-dash"></i></span>
                                    <div class="">{!! $manager->name !!}</div>
                                    <div class="">{!! \App\Helpers\Helper::mailTo($manager->email) !!}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">
                            <i class="fa-regular fa-info-circle me-2"></i>
                            Aucun Manager assigné à cette société et/ou compte pour approbation.
                        </p>
                    @endif
                </div>
            </div>

            {{-- TeamViewer --}}
            <div class="card border border-primary border-opacity-50 mb-4">
                <div class="card-header bg-primary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-desktop"></i> {{ config('app.teamviewer_name') }}</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        Si un technicien vous invite à accéder à votre poste, vous pouvez télécharger une version sécurisée
                        de
                        {{ config('app.teamviewer_name') }}.
                    </p>
                    <a href="{{ config('app.teamviewer_url') }}" target="_blank"
                        class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa-regular fa-download me-2"></i>Télécharger {{ config('app.teamviewer_name') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
