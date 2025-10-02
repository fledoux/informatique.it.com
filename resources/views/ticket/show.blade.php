@extends('layouts.app')

@section('title', __('global.Details') . ' ' . __('ticket.entity'))

@section('content')
    <h4 class="mb-3">
        <i class="fa-regular fa-comments me-2"></i>
        Conversation
    </h4>

    <div class="d-block d-sm-none">
        @include('ticket._addTicket')
    </div>

    <div class="row">
        <div class="col-12 col-lg-9">
            <div class="mt-5">

                {{-- Message support --}}
                <div class="row">
                    <div class="col-2 mb-3 text-end text-warning-emphasis">
                        <span class="fw-bold">Votre support</span><br>
                        <small class="text-muted">14:30</small>
                    </div>
                    <div class="col-12 col-lg-10 mb-3">
                        <div class="card border border-warning border-2">
                            <div class="card-body bg-warning-subtle">
                                <p class="mb-1">Bonjour, nous avons bien reçu votre demande concernant le problème réseau.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Message client --}}
                <div class="row">
                    <div class="col-12, col-lg-2 mb-3 text-end">
                        <span class="text-secondary fw-bold">{{ $ticket->author->name ?? '' }}</span><br>
                        <small
                            class="text-muted">{{ $ticket->created_at->format('d/m/Y') ?? '' }}<br>{{ $ticket->created_at->format('H\hi') ?? '' }}</small>
                    </div>
                    <div class="col-12 col-lg-10 mb-3 ms-auto">
                        <div class="card border  border-2">
                            <div class="card-body">
                                <strong>{{ $ticket->subject }}</strong><br>
                                {{ $ticket->question }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card border border-secondary mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="h6 mb-0 fw-bold"><i class="fa-regular fa-ticket"></i> Ticket #{{ $ticket->id }}</h3>
                </div>
                <div class="card-body">
                    <p><small class="text-primary">{{ $ticket->created_at->format('l, d F Y à H\hi') ?? '' }}</small></p>
                    <p><i class="fa-solid fa-square-check text-success"></i> {!! __('ticket.yes') !!},
                        {{ $ticket->author_id ? \App\Models\User::find($ticket->author_id)?->name : '' }}
                        {!! __('ticket.cgv') !!}</p>
                    <span
                        class="badge mb-3 {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">{{ __('ticket.status.' . $ticket->status) }}</span>
                    <span
                        class="badge mb-3 {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">{{ __('ticket.priorityFull.' . $ticket->priority) }}</span>

                    <p>
                        <span
                            class="h4">{{ $ticket->company_id ? \App\Models\Company::find($ticket->company_id)?->name : '' }}</span><br>
                        {{ $ticket->author_id ? \App\Models\User::find($ticket->author_id)?->name : '' }}<br>
                        <small class="text-muted">{{ $ticket->author?->getRoleNames()->implode(', ') }}</small><br>
                        {!! \App\Helpers\Helper::mailTo(\App\Models\User::find($ticket->author_id)->email) !!}<br>
                        {!! $ticket->author_id
                            ? '<a href="tel:' .
                                \App\Models\User::find($ticket->author_id)?->phone .
                                '" class="text-orange text-decoration-none">' .
                                \App\Models\User::find($ticket->author_id)?->phone .
                                '</a>'
                            : '' !!}
                    </p>
                    <p>
                        <span class="text-muted">{{ __('ticket.fields.folder_code') }} :
                        </span>{{ $ticket->folder_code ?? '' }}<br>
                        <span class="text-muted">{{ __('ticket.fields.due') }} :
                        </span>{{ $ticket->due ? ($ticket->due instanceof \Carbon\Carbon ? $ticket->due->format('d/m/y à H\hi') : $ticket->due) : '' }}<br>
                        <span class="text-muted">{{ __('ticket.fields.assigned_to') }} :
                        </span>{{ $ticket->assignedTo?->name ?? '' }}<br>
                        <span class="text-muted">{{ __('ticket.fields.assigned_at') }} :
                        </span>{{ $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('d/m/y à H\hi') : $ticket->assigned_at) : '' }}<br>

                        <span
                            class="{{ $ticket->billable ? 'text-muted' : 'text-danger' }}">{{ __('ticket.fields.billable') }}
                            : </span>{!! $ticket->billable ? __('ticket.billable.yes') : __('ticket.billable.no') !!}
                    </p>
                    <div class="d-none d-sm-flex mt-3">
                        @include('ticket._addTicket')
                    </div>
                </div>
            </div>

            <div class="card border mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-money-bill-1"></i> Facturation</h3>
                </div>
                <div class="card-body pb-0">
                    <p>
                        Estimation : <span class="float-end">2 tickets</span><br>
                        Facturation : <span class="float-end">1 ticket</span><br>
                        Majoration : <span class="float-end">0 ticket</span>
                    </p>
                </div>
                <div class="card-footer bg-success-subtle">
                    <span class="text-success fw-bold">Solde au {{ now()->format('d/m/y') }} : <span class="float-end">12
                            {{ abs(12) > 1 ? __('ticket.tickets') : __('ticket.ticket') }}</span></span><br>
                </div>
            </div>

            <div class="card border mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-file-import"></i> Fichiers joints</h3>
                </div>
                <div class="card-body pb-0">
                    <p claass="p-0">ici2</p>
                </div>
            </div>
            <div class="card border mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-thumbs-up"></i> Demande d'Approbation</h3>
                </div>
                <div class="card-body pb-0">
                    @php
                        $managers = $ticket->getManagers();
                    @endphp

                    @if ($managers->count() > 0)
                        Une approbation est requise uniquement pour les demandes liées à la sécurité.<br>Voici la liste des approbateurs actuels :
                        </p>
                        <ul class="small fa-ul">
                            @foreach ($managers as $manager)
                                <li class="mb-2">
                                    <span class="fa-li"><i class="fa-solid fa-dash"></i></span>
                                    <div class="">{!! $manager->name !!}</div>
                                    <div class="">{!! \App\Helpers\Helper::mailTo($manager->email) !!}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Aucun Manager assigné à cette société et/ou compte pour approbation.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
