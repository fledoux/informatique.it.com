@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('manager'))
{{ __('ticket_message.List') }}
@else  
{{ __('ticket_message.YourList') }}
@endif
@endsection

@section('content')
<h1 class="mb-4">{{ __('ticket_message.List') }}</h1>

<div class="table-responsive">
<table class="table align-middle table-xs table-bordered table-hover">
<thead>
<tr>
<th class="text-center">{{ __('ticket_message.id') }}</th>
<th class="text-left">{{ __('ticket_message.fields.status') }}</th>
<th class="text-left">{{ __('ticket_message.fields.subject') }}</th>
<th class="text-left">{{ __('ticket_message.fields.body') }}</th>
<th class="text-left">{{ __('ticket_message.fields.company_id') }}</th>
<th class="text-left">{{ __('ticket_message.fields.ticket_id') }}</th>
<th class="text-left">{{ __('ticket_message.fields.author_id') }}</th>
<th>{{ __('global.Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse($ticketMessages as $ticketMessage)
<tr>
<td class="text-center">{{ $ticketMessage->id }}</td>
<td>{{ $ticketMessage->status }}</td>
<td>{{ $ticketMessage->subject }}</td>
<td>{{ $ticketMessage->body }}</td>
<td>{{ $ticketMessage->company?->name ?? '' }}</td>
<td>{{ $ticketMessage->ticket?->public_uuid ?? '' }}</td>
<td>{{ $ticketMessage->author?->name ?? '' }}</td>
<td class="text-nowrap">
<a href="{{ route('ticket_message.show', $ticketMessage) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.Details') !!}
</a>
<a href="{{ route('ticket_message.edit', $ticketMessage) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.Edit') !!}
</a>
@include('ticket_message._delete_form', ['ticketMessage' => $ticketMessage])
</td>
</tr>
@empty
<tr>
<td colspan="8" class="text-center">
{{ __('global.No data') }}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('ticket_message.create') }}" class="btn btn-orange mt-3">
    <i class="fa-regular fa-square-plus"></i>
{{ __('global.New') }}
</a>
@endsection