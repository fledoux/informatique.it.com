@extends('layouts.app')

@section('title')
    {{ __('ticket.List') }}
@endsection

@section('content')
    <h1 class="mb-4">{{ __('ticket.List') }}</h1>

    @if($tickets->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-xs table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('ticket.Id') }}</th>
                        <th class="text-left">{{ __('ticket.fields.status') }}</th>
                        <th class="text-left">{{ __('ticket.fields.priority') }}</th>
                        @can('company.show')
                            <th class="text-left">{{ __('ticket.fields.company_id') }}</th>
                        @endcan
                        <th class="text-left">{{ __('ticket.fields.author_id') }}</th>
                        <th class="text-left">{{ __('ticket.fields.subject') }}</th>
                        @can('ticket.edit')
                            <th class="text-left">{{ __('ticket.fields.assigned_to') }}</th>
                        @endcan
                        <th class="text-left">{{ __('ticket.fields.due') }}</th>
                        <th class="text-left">{{ __('ticket.fields.folder_code') }}</th>
                        <th class="text-left">{{ __('ticket.fields.billable') }}</th>
                        <th>{{ __('global.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td class="text-center">{{ $ticket->id }}</td>
                        <td>
                            <span
                                class="badge {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">{{ __('ticket.status.' . $ticket->status) }}</span>
                        </td>
                        <td>
                            <span
                                class="badge {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">{{ __('ticket.priority.' . $ticket->priority) }}</span>
                        </td>
                        @can('company.show')
                            <td>{{ $ticket->company?->name ?? '' }}</td>
                        @endcan
                        <td>{{ $ticket->author ? \App\Helpers\Helper::getFullName($ticket->author->firstname, $ticket->author->lastname, $ticket->author->name) : '' }}
                        </td>
                        <td>{{ $ticket->subject }}</td>
                        @can('ticket.edit')
                            <td>{{ $ticket->assignedTo?->initial ?? '' }}</td>
                        @endcan
                        <td>{{ $ticket->due ? $ticket->due->format('d/m H:i') : '' }}</td>
                        <td>{{ $ticket->folder_code }}</td>
                        <td>{{ $ticket->billable ? __('global.boolean.yes') : __('global.boolean.no') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('ticket.show', $ticket) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Details') !!}
                            </a>
                            @can('ticket.edit')
                                <a href="{{ route('ticket.edit', $ticket) }}"
                                    class="btn btn-link text-decoration-none p-0 me-2">
                                    {!! __('global.Edit') !!}
                                </a>
                            @endcan
                            @can('ticket.delete')
                                @include('ticket._delete_form', ['ticket' => $ticket])
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else        
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fa-light fa-message-smile text-orange mb-4" style="font-size: 5rem;"></i>
                <h3 class="text-muted mb-3">{{ __('ticket.empty_state.title') }}</h3>
                @can('ticket.create')
                    <a href="{{ route('ticket.create') }}" class="btn btn-orange">
                        <i class="fa-regular fa-square-plus"></i>
                        {{ __('ticket.empty_state.create_button') }}
                    </a>
                @endcan
            </div>
        </div>
    @endif

    @if($tickets->count() > 0)
        <a href="{{ route('ticket.create') }}" class="btn btn-orange mt-3">
            <i class="fa-regular fa-square-plus"></i>
            {{ __('global.New') }}
        </a>
    @endif
@endsection
