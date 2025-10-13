@extends(auth()->user()->hasRole('super-admin') ? 'layouts.app-fluid' : 'layouts.app')

@section('title')
    {{ __('ticket.List') }}
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            {!! __('ticket.h1.List') !!}
        </h1>
        @can('create', App\Models\Ticket::class)
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('ticket.create') }}" class="btn btn-orange">
                    {!! __('btn.NewTicket') !!}
                </a>
            </div>
        @endcan
    </div>

    @if ($tickets->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle datatable table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('ticket.Id') }}</th>
                        <th class="text-center">{{ __('ticket.fields.status') }}</th>
                        <th class="text-center">{{ __('ticket.fields.priority') }}</th>
                        <th class="text-center">{{ __('ticket.fields.billable') }}</th>
                        @hasanyrole(['super-admin'])
                            <th class="text-center">Cactus</th>
                        @endhasanyrole
                        @hasanyrole(['super-admin'])
                            <th class="text-center">Majo</th>
                        @endhasanyrole
                        <th class="text-left">{{ __('ticket.fields.folder_code') }}</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">{{ __('ticket.fields.due') }}</th>
                        @hasanyrole(['super-admin'])
                            <th class="text-left">{{ __('ticket.fields.company_id') }}</th>
                        @endhasanyrole
                        <th class="text-left">{{ __('ticket.Author') }}</th>
                        <th class="text-left">{{ __('ticket.fields.subject') }}</th>
                        <th>{{ __('global.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr class="{{ $ticket->needsReply() ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $ticket->id }}</td>
                            <td class="text-center">
                                <span
                                    class="badge {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">{{ __('ticket.status.' . $ticket->status) }}</span>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">{{ __('ticket.priority.' . $ticket->priority) }}
                                </span>
                            </td>
                            <td class="text-center">{!! $ticket->billable ? __('ticket.billableindex.yes') : __('ticket.billableindex.no') !!}</td>
                            @hasanyrole(['super-admin'])
                                <td class="text-center">Cactus</td>
                            @endhasanyrole
                            @hasanyrole(['super-admin'])
                                <td class="text-center">Majo</td>
                            @endhasanyrole
                            <td>{{ $ticket->folder_code }}</td>
                            <td>{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '' }}</td>
                            <td>{{ $ticket->due ? $ticket->due->format('d/m H:i') : '' }}</td>
                            @hasanyrole(['super-admin'])
                                <td class="text-left">
                                    {{ $ticket->company?->name ?? '' }}<br>
                                </td>
                            @endhasanyrole
                            <td class="text-left">
                                {{ $ticket->author ? \App\Helpers\Helper::getFullName($ticket->author->firstname, $ticket->author->lastname, $ticket->author->name) : '' }}
                            </td>
                            <td>{{ $ticket->subject }}</td>
                            <td class="text-nowrap">
                                @can('ticket.show')
                                    <a href="{{ route('ticket.show', $ticket) }}"
                                        class="btn btn-link text-decoration-none p-0 me-2">
                                        {!! __('global.btn.Details') !!}
                                    </a>
                                @endcan
                                @hasanyrole(['super-admin'])
                                    @can('ticket.edit')
                                        <a href="{{ route('ticket.edit', $ticket) }}"
                                            class="btn btn-link text-decoration-none p-0 me-2">
                                            {!! __('global.btn.Edit') !!}
                                        </a>
                                    @endcan
                                    @can('ticket.delete')
                                        @include('ticket._delete_form', ['ticket' => $ticket])
                                    @endcan
                                @endhasanyrole
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2 p-sm-3 text-center py-5">
                <i class="fa-light fa-message-smile text-orange mb-4" style="font-size: 5rem;"></i>
                <h3 class="text-muted mb-3">{{ __('ticket.empty_state.title') }}</h3>
                @can('ticket.create')
                    <a href="{{ route('ticket.create') }}" class="btn btn-orange">

                        {{ __('ticket.empty_state.create_button') }}
                    </a>
                @endcan
            </div>
        </div>
    @endif
@endsection

@if ($tickets->count() > 0)
    @push('javascripts')
        @include('partials._datatable')
    @endpush
@endif
