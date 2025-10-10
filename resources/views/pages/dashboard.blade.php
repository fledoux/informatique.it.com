@extends('layouts.app')

@section('title', __('dashboard.Welcome'))

@section('content')
    {{-- Titre + actions rapides --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-5">
            <h1 class="h3 mb-3 mb-sm-0">
                <i class="fa-light fa-gauge"></i>
                {{ __('dashboard.WelcomeTitle') }}
            </h1>
            <div class="d-flex flex-column flex-sm-row flex-wrap gap-1 gap-sm-2 ms-md-auto">
                @auth
                    <a href="{{ route('ticket.create') }}" class="btn btn-orange w-100 w-sm-auto">
                        {!! __('btn.NewTicket') !!}
                    </a>
                @endauth
                @can('user.create')
                    <a href="{{ route('user.create') }}" class="btn btn-outline-secondary w-100 w-sm-auto">
                        {!! __('btn.NewUser') !!}
                    </a>
                @endcan
                @hasanyrole(['super-admin'])
                    <a href="{{ route('company.create') }}" class="btn btn-outline-danger w-100 w-sm-auto">
                        {!! __('btn.NewCompany') !!}
                    </a>
                @endhasanyrole
                @hasanyrole(['super-admin'])
                    <a href="{{ route('permissions.index') }}" class="btn btn-outline-danger w-100 w-sm-auto">
                        {!! __('btn.Permissions') !!}
                    </a>
                @endhasanyrole
            </div>
        </div>
        {{-- KPIs --}}
        <div class="row g-3 mb-5">
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
                        <i class="fa-light fa-message-question fs-3 text-orange"></i>
                        <div>
                            <div class="text-secondary small">{{ __('dashboard.KPI.Tickets') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['tickets_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <div
                    class="card shadow-sm h-100 {{ $ticketStats['open_tickets_count'] > 0 ? 'bg-danger text-white' : '' }}">
                    <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
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
                    <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
                        <i class="fa-light fa-clock fs-3 {{ $ticketStats['waiting_count'] > 0 ? '' : 'text-orange' }}"></i>
                        <div>
                            <div class="text-secondary small">{{ __('dashboard.KPI.Waiting') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['waiting_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <div class="card shadow-sm h-100 {{ $ticketStats['overdue_count'] > 0 ? 'bg-danger text-white' : '' }}">
                    <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
                        <i
                            class="fa-light fa-triangle-exclamation fs-3 {{ $ticketStats['overdue_count'] > 0 ? 'text-white' : 'text-orange' }}"></i>
                        <div>
                            <div class="small">{{ __('dashboard.KPI.Overdue') }}</div>
                            <div class="fs-4 fw-semibold">{{ $ticketStats['overdue_count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @can('contact.index')
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
                            <i class="fa-light fa-address-book fs-3 text-orange"></i>
                            <div>
                                <div class="text-secondary small">{{ __('dashboard.KPI.Contacts') }}</div>
                                <div class="fs-4 fw-semibold">{{ $contactsCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            @can('company.index')
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-1 gap-sm-3">
                            <i class="fa-light fa-buildings fs-3 text-orange"></i>
                            <div>
                                <div class="text-secondary small">{{ __('dashboard.KPI.Companies') }}</div>
                                <div class="fs-4 fw-semibold">{{ $companiesCount }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
        {{-- Derniers tickets --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h2 class="h6 mb-0">
                    <i class="fa-light fa-clock-rotate-left me-2"></i>
                    {{ $lastXTickets }} {{ __('dashboard.RecentTickets') }}
                </h2>
                <a href="{{ route('ticket.index') }}" class="btn btn-sm btn-link text-decoration-none">
                    {!! __('btn.ViewAll') !!} →
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-nowrap text-center">{{ __('ticket.Id') }}</th>
                                <th class="text-center">{{ __('ticket.Status') }}</th>
                                <th class="text-center">{{ __('ticket.Priority') }}</th>
                                <th>{{ __('ticket.Subject') }}</th>
                                @hasanyrole(['super-admin'])
                                    <th>{{ __('ticket.Company') }}</th>
                                @endhasanyrole
                                @unless (auth()->user()->hasRole('user'))
                                    <th>{{ __('ticket.Author') }}</th>
                                @endunless
                                <th>{{ __('ticket.Create at') }}</th>
                                <th>{{ __('ticket.DueAt') }}</th>
                                <th class="text-center">{{ __('global.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                                <tr>
                                    <td class="text-secondary text-center">{{ $ticket->id }}</td>
                                    <td class="text-center">
                                        @if ($ticket->status)
                                            <span class="badge {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">
                                                {{ __('ticket.status.' . $ticket->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($ticket->priority)
                                            <span class="badge {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">
                                                {{ __('ticket.priority.' . $ticket->priority) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-truncate">{{ \Illuminate\Support\Str::limit($ticket->subject, 45) }}</td>
                                    @hasanyrole(['super-admin'])
                                        <td>{{ $ticket->company?->name ?? '' }}</td>
                                    @endhasanyrole
                                    @unless (auth()->user()->hasRole('user'))
                                        <td>{{ $ticket->author ? \App\Helpers\Helper::getFullName($ticket->author->firstname, $ticket->author->lastname, $ticket->author->name) : '' }}
                                        </td>
                                    @endunless
                                    <td>{{ $ticket->due ? $ticket->created_at->format('d/m H:i') : '' }}</td>
                                     <td>{{ $ticket->due ? $ticket->due->format('d/m H:i') : '' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('ticket.show', $ticket) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('user') ? '8' : '9' }}"
                                        class="text-center text-secondary py-4">
                                        {{ __('dashboard.NoTicketsYet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
