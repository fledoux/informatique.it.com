@extends('layouts.app')

@section('title')
    @if (auth()->check() && auth()->user()->hasRole('manager'))
        {{ __('ticket.List') }}
    @else
        {{ __('ticket.YourList') }}
    @endif
@endsection

@section('content')
    <h1 class="mb-4">{{ __('ticket.List') }}</h1>

    <div class="table-responsive-lg">
        <table class="table align-middle table-xs table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('ticket.Id') }}</th>
                    <th class="text-left">{{ __('ticket.fields.status') }}</th>
                    <th class="text-left">{{ __('ticket.fields.priority') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('ticket.fields.company_id') }}</th>
                    @endrole
                    <th class="text-left">{{ __('ticket.fields.author_id') }}</th>
                    <th class="text-left">{{ __('ticket.fields.subject') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('ticket.fields.assigned_to') }}</th>
                    @endrole
                    <th class="text-left">{{ __('ticket.fields.due') }}</th>
                    <th class="text-left">{{ __('ticket.fields.folder_code') }}</th>
                    <th class="text-left">{{ __('ticket.fields.billable') }}</th>
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
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
                        @role('super-admin')
                            <td>{{ $ticket->company?->name ?? '' }}</td>
                        @endrole
                        <td>{{ $ticket->author ? \App\Helpers\Helper::getFullName($ticket->author->firstname, $ticket->author->lastname, $ticket->author->name) : '' }}
                        </td>
                        <td>{{ $ticket->subject }}</td>
                        @role('super-admin')
                            <td>{{ $ticket->assignedTo?->initial ?? '' }}</td>
                        @endrole
                        <td>{{ $ticket->due ? $ticket->due->format('d/m H:i') : '' }}</td>
                        <td>{{ $ticket->folder_code }}</td>
                        <td>{{ $ticket->billable ? __('global.boolean.yes') : __('global.boolean.no') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('ticket.show', $ticket) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Details') !!}
                            </a>
                            @role('super-admin')
                                <a href="{{ route('ticket.edit', $ticket) }}"
                                    class="btn btn-link text-decoration-none p-0 me-2">
                                    {!! __('global.Edit') !!}
                                </a>
                            @endrole
                            @role('super-admin')
                                @include('ticket._delete_form', ['ticket' => $ticket])
                            @endrole
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center">
                            {{ __('global.No data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('ticket.create') }}" class="btn btn-orange mt-3">
        <i class="fa-regular fa-square-plus"></i>
        {{ __('global.New') }}
    </a>
@endsection
