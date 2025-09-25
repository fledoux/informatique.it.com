@extends('layouts.app')

@section('title', __('global.Details') . ' — ' . __('ticket.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Details') !!} — {{ __('ticket.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('ticket.id') }}</dt>
        <dd class="col-sm-9">{{ $ticket->id }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.status') }}</dt>
        <dd class="col-sm-9">
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
        </dd>
        <dt class="col-sm-3">{{ __('ticket.fields.priority') }}</dt>
        <dd class="col-sm-9">
            @php($badgeColor = 'secondary')
            @switch($ticket->priority)
                @case('low') @php($badgeColor = 'info') @break
                @case('normal') @php($badgeColor = 'primary') @break
                @case('high') @php($badgeColor = 'danger') @break
                @case('urgent') @php($badgeColor = 'danger') @break
            @endswitch
            <span class="badge bg-{{ $badgeColor }}">{{ __('ticket.priority.' . $ticket->priority) }}</span>
        </dd>
        <dt class="col-sm-3">{{ __('ticket.fields.company_id') }}</dt>
        <dd class="col-sm-9">{{ $ticket->company_id ? \App\Models\Company::find($ticket->company_id)?->name : '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.author_id') }}</dt>
        <dd class="col-sm-9">{{ $ticket->author_id ? \App\Models\User::find($ticket->author_id)?->name : '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.assigned_to') }}</dt>
        <dd class="col-sm-9">{{ $ticket->assigned_to ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.assigned_at') }}</dt>
        <dd class="col-sm-9">{{ $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('d/m/Y à H:i') : $ticket->assigned_at) : '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.due') }}</dt>
        <dd class="col-sm-9">{{ $ticket->due ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.folder_code') }}</dt>
        <dd class="col-sm-9">{{ $ticket->folder_code ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.subject') }}</dt>
        <dd class="col-sm-9">{{ $ticket->subject ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.question') }}</dt>
        <dd class="col-sm-9">{{ $ticket->question ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('ticket.fields.billable') }}</dt>
        <dd class="col-sm-9">{{ $ticket->billable ? __('global.boolean.yes') : __('global.boolean.no') }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('ticket.edit', $ticket) }}" class="btn btn-primary">{!! __('global.Edit') !!}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
    </div>
@endsection