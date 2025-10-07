@extends('layouts.app')

@section('title', __('global.Details') . '  ' . __('ticketmessage.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.btn.Details') !!}  {{ __('ticketmessage.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('ticketmessage.id') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->id }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.status') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->status ?? '' }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.subject') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->subject ?? '' }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.body') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->body ?? '' }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.company_id') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->company_id ? \App\Models\Company::find($ticketMessage->company_id)?->name : '' }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.ticket_id') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->ticket_id ? \App\Models\Ticket::find($ticketMessage->ticket_id)?->public_uuid : '' }}</dd>
        <dt class="col-sm-3">{{ __('ticketmessage.fields.author_id') }}</dt>
        <dd class="col-sm-9">{{ $ticketMessage->author_id ? \App\Models\User::find($ticketMessage->author_id)?->name : '' }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('ticketmessage.edit', $ticketMessage) }}" class="btn btn-primary">{!! __('global.btn.Edit') !!}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
    </div>
@endsection