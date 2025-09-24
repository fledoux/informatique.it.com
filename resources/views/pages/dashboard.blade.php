@extends('layouts.app')

@section('title', __('dashboard.Welcome'))

@section('content')

    {{ auth()->user()->getRoleNames()->implode(', ') }}
    <p>Société: {{ auth()->user()->company?->id ?? 'Aucune société' }}</p>

    <div class="container-xxl py-4">

        {{-- Titre + actions rapides --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-5">
            <h1 class="h3 mb-3 mb-sm-0">
                <i class="fa-light fa-gauge"></i>
                {{ __('dashboard.WelcomeTitle') }}
            </h1>
            <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 ms-md-auto">
                @auth
                    <a href="{{ route('ticket.create') }}" class="btn btn-orange w-100 w-sm-auto">
                        <i class="fa-regular fa-message-question"></i>
                        {{ __('btn.NewTicket') }}
                    </a>
                @endauth
                @can('admin.access')
                    <a href="{{ route('user.create') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                        <i class="fa-regular fa-user-plus"></i>
                        {{ __('btn.NewUser') }}
                    </a>
                @endcan
                @can('admin.access')
                    <a href="{{ route('company.create') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                        <i class="fa-regular fa-building"></i>
                        {{ __('btn.NewCompany') }}
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
                            <div class="text-muted small">{{ __('dashboard.KPI.Tickets') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['tickets_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <div
                    class="card shadow-sm h-100 {{ $ticketStats['open_tickets_count'] > 0 ? 'bg-warning' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i
                            class="fa-light fa-clipboard-list-check fs-3 {{ $ticketStats['open_tickets_count'] > 0 ? '' : 'text-orange' }}"></i>
                        <div>
                            <div class="small">{{ __('dashboard.KPI.Open') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['open_tickets_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100 {{ $ticketStats['waiting_count'] > 0 ? 'bg-warning' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-light fa-clock fs-3 {{ $ticketStats['waiting_count'] > 0 ? '' : 'text-orange' }}"></i>
                        <div>
                            <div class="text-muted small">{{ __('dashboard.KPI.Waiting') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['waiting_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100 {{ $ticketStats['overdue_count'] > 0 ? 'bg-danger text-white' : '' }}">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i
                            class="fa-light fa-triangle-exclamation fs-3 {{ $ticketStats['overdue_count'] > 0 ? 'text-white' : 'text-orange' }}"></i>
                        <div>
                            <div class="small">{{ __('dashboard.KPI.Overdue') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['overdue_count'] }}</div>
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
                                <div class="text-muted small">{{ __('dashboard.KPI.Contacts') }}</div>
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
                                <div class="text-muted small">{{ __('dashboard.KPI.Companies') }}</div>
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
                    {{ $lastXTickets }} {{ __('dashboard.RecentTickets') }}
                </h2>
                <a href="{{ route('ticket.index') }}" class="btn btn-sm btn-link text-decoration-none">
                    {{ __('btn.ViewAll') }} →
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap text-center">{{ __('ticket.Id') }}</th>
                                <th>{{ __('ticket.Status') }}</th>
                                <th>{{ __('ticket.Priority') }}</th>
                                <th>{{ __('ticket.Subject') }}</th>
                                <th>{{ __('ticket.Company') }}</th>
                                <th>{{ __('ticket.AssignedTo') }}</th>
                                <th>{{ __('ticket.DueAt') }}</th>
                                <th class="text-end">{{ __('global.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                                <tr>
                                    <td class="text-muted text-center">{{ $ticket->id }}</td>
                                    <td>
                                        @if ($ticket->status)
                                            <span class="badge bg-{{ __('ticket.statusClass.' . $ticket->status) }}">
                                                {{ __('ticket.status.' . $ticket->status) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if ($ticket->priority)
                                            <span class="badge bg-light text-dark">
                                                {{ __('ticket.priority.' . $ticket->priority) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width:320px">{{ $ticket->subject }}</td>
                                    <td>{{ $ticket->company?->name ?? '—' }}</td>
                                    <td>{{ $ticket->assignedTo?->email ?? '—' }}</td>
                                    <td>{{ $ticket->due ? $ticket->due->format('d/m H:i') : '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('ticket.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        {{ __('dashboard.NoTicketsYet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection
