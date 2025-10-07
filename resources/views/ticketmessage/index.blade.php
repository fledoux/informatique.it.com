@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('manager'))
{{ __('ticketmessage.List') }}
@else  
{{ __('ticketmessage.YourList') }}
@endif
@endsection

@section('content')
<h1 class="mb-4">{{ __('ticketmessage.List') }}</h1>

<div class="table-responsive">
<table class="table align-middle table-xs table-bordered table-hover">
<thead>
<tr>
<th class="text-center">{{ __('ticketmessage.id') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.status') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.subject') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.body') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.company_id') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.ticket_id') }}</th>
<th class="text-left">{{ __('ticketmessage.fields.author_id') }}</th>
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
<a href="{{ route('ticketmessage.show', $ticketMessage) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.btn.Details') !!}
</a>
<a href="{{ route('ticketmessage.edit', $ticketMessage) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.btn.Edit') !!}
</a>
@include('ticketmessage._delete_form', ['ticketMessage' => $ticketMessage])
</td>
</tr>
@empty
<tr>
<td colspan="8" class="text-center">
{!! __('global.No data') !!}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('ticketmessage.create') }}" class="btn btn-orange mt-3">
    
{!! __('global.btn.New') !!}
</a>
@endsection