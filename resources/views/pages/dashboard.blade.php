@extends('layouts.app')

@section('title', __('Dashboard.Welcome'))

@section('content')

<div class="container-xxl py-4">

    {{-- Titre + actions rapides --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-5">
        <h1 class="h3 mb-3 mb-sm-0">
            <i class="fa-light fa-gauge"></i>
            {{ __('Dashboard.WelcomeTitle') }}
        </h1>
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 ms-md-auto">
            @auth
                <a href="{{ route('ticket.create') }}" class="btn btn-orange w-100 w-sm-auto">
                    {{ __('Btn.NewTicket') }}
                </a>
            @endauth
            @can('admin.access')
                <a href="{{ route('user.create') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                    {{ __('Btn.NewUser') }}
                </a>
            @endcan
            @can('admin.access')
                <a href="{{ route('company.create') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                    {{ __('Btn.NewCompany') }}
                </a>
            @endcan
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-5">
        <div class="col-12 col-md-4 col-xl-2">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fa-light fa-message-question fs-3 text-orange"></i>
                    <div>
                        <div class="text-muted small">{{ __('Dashboard.KPI.Tickets') }}</div>
                        <div class="fs-4 fw-semibold">{{ $ticketsCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-2">
            <div class="card shadow-sm h-100 {{ $openTicketsCount > 0 ? 'bg-danger text-white' : '' }}">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fa-light fa-clipboard-list-check fs-3 {{ $openTicketsCount > 0 ? 'text-white' : 'text-orange' }}"></i>
                    <div>
                        <div class="small">{{ __('Dashboard.KPI.Open') }}</div>
                        <div class="fs-4 fw-semibold">{{ $openTicketsCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-2">
            <div class="card shadow-sm h-100 {{ $waitingCount > 0 ? 'bg-warning' : '' }}">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fa-light fa-clock fs-3 {{ $waitingCount > 0 ? '' : 'text-orange' }}"></i>
                    <div>
                        <div class="text-muted small">{{ __('Dashboard.KPI.Waiting') }}</div>
                        <div class="fs-4 fw-semibold">{{ $waitingCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-2">
            <div class="card shadow-sm h-100 {{ $overdueCount > 0 ? 'bg-danger text-white' : '' }}">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fa-light fa-triangle-exclamation fs-3 {{ $overdueCount > 0 ? 'text-white' : 'text-orange' }}"></i>
                    <div>
                        <div class="small">{{ __('Dashboard.KPI.Overdue') }}</div>
                        <div class="fs-4 fw-semibold">{{ $overdueCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        @can('admin.access')
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-light fa-address-book fs-3 text-orange"></i>
                        <div>
                            <div class="text-muted small">{{ __('Dashboard.KPI.Contacts') }}</div>
                            <div class="fs-4 fw-semibold">{{ $contactsCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('admin.access')
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-light fa-buildings fs-3 text-orange"></i>
                        <div>
                            <div class="text-muted small">{{ __('Dashboard.KPI.Companies') }}</div>
                            <div class="fs-4 fw-semibold">{{ $companiesCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    {{-- Derniers tickets --}}
    <div class="card shadow-sm border-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 mb-0">
                <i class="fa-light fa-clock-rotate-left me-2"></i>
                {{ __('Dashboard.RecentTickets') }}
            </h2>
            <a href="{{ route('ticket.index') }}" class="btn btn-sm btn-link text-decoration-none">
                {{ __('Btn.ViewAll') }} →
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap text-center">{{ __('Ticket.Id') }}</th>
                            <th>{{ __('Ticket.Ticket_status') }}</th>
                            <th>{{ __('Ticket.Priority') }}</th>
                            <th>{{ __('Ticket.Subject') }}</th>
                            <th>{{ __('Ticket.Company') }}</th>
                            <th>{{ __('Ticket.AssignedTo') }}</th>
                            <th>{{ __('Ticket.DueAt') }}</th>
                            <th class="text-end">{{ __('Global.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                            <tr>
                                <td class="text-muted text-center">{{ $ticket->id }}</td>
                                <td>
                                    @if($ticket->status)
                                        <span class="badge bg-{{ __('Ticket.statusClass.' . $ticket->status) }}">
                                            {{ __('Ticket.status.' . $ticket->status) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->priority)
                                        <span class="badge bg-light text-dark">
                                            {{ __('Ticket.priority.' . $ticket->priority) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width:320px">{{ $ticket->subject }}</td>
                                <td>{{ $ticket->company?->name ?? '—' }}</td>
                                <td>{{ $ticket->assignedTo?->email ?? '—' }}</td>
                                <td>{{ $ticket->due_at ? $ticket->due_at->format('d/m/Y H:i') : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('ticket.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ticket.edit', $ticket) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Aucun ticket pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection