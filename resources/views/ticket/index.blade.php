@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('admin'))
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
<th class="text-center">{{ __('ticket.id') }}</th>
<th class="text-left">{{ __('ticket.fields.status') }}</th>
<th class="text-left">{{ __('ticket.fields.priority') }}</th>
<th class="text-left">{{ __('ticket.fields.company_id') }}</th>
<th class="text-left">{{ __('ticket.fields.author_id') }}</th>
<th class="text-left">{{ __('ticket.fields.assigned_to') }}</th>
<th class="text-left">{{ __('ticket.fields.assigned_at') }}</th>
<th class="text-left">{{ __('ticket.fields.due') }}</th>
<th class="text-left">{{ __('ticket.fields.folder_code') }}</th>
<th class="text-left">{{ __('ticket.fields.subject') }}</th>
<th class="text-left">{{ __('ticket.fields.question') }}</th>
<th class="text-left">{{ __('ticket.fields.billable') }}</th>
<th>{{ __('global.Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse($tickets as $ticket)
<tr>
<td class="text-center">{{ $ticket->id }}</td>
<td>
@php($badgeColor = 'secondary')
@switch($ticket->status)
    @case('new') @php($badgeColor = 'info') @break
    @case('in_progress') @php($badgeColor = 'primary') @break
    @case('waiting') @php($badgeColor = 'warning') @break
    @case('resolved') @php($badgeColor = 'primary') @break
    @case('closed') @php($badgeColor = 'secondary') @break
    @case('canceled') @php($badgeColor = 'primary') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('ticket.status.' . $ticket->status) }}</span>
</td>
<td>
@php($badgeColor = 'secondary')
@switch($ticket->priority)
    @case('low') @php($badgeColor = 'info') @break
    @case('normal') @php($badgeColor = 'primary') @break
    @case('high') @php($badgeColor = 'danger') @break
    @case('urgent') @php($badgeColor = 'danger') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('ticket.priority.' . $ticket->priority) }}</span>
</td>
<td>{{ $ticket->company?->name ?? '—' }}</td>
<td>{{ $ticket->author?->name ?? '—' }}</td>
<td>{{ $ticket->assigned_to }}</td>
<td>{{ $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('d/m/Y H:i') : $ticket->assigned_at) : '—' }}</td>
<td>{{ $ticket->due }}</td>
<td>{{ $ticket->folder_code }}</td>
<td>{{ $ticket->subject }}</td>
<td>{{ $ticket->question }}</td>
<td>{{ $ticket->billable ? __('global.boolean.yes') : __('global.boolean.no') }}</td>
<td class="text-nowrap">
<a href="{{ route('ticket.show', $ticket) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('global.Details') }}
</a>
<a href="{{ route('ticket.edit', $ticket) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('global.Edit') }}
</a>
@include('ticket._delete_form', ['ticket' => $ticket])
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