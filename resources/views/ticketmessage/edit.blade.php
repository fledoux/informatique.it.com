@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('ticketmessage.entity'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{!! __('global.btn.Edit') !!} {{ __('ticketmessage.entity') }}</h1>
        <a href="{{ route('ticket.show', $ticketMessage->ticket_id) }}" class="btn btn-secondary">
            <i class="fa-regular fa-arrow-left me-2"></i>Retour au ticket
        </a>
    </div>

    <form method="POST" action="{{ route('ticketmessage.update', $ticketMessage) }}" novalidate>
        @csrf
        @method('PUT')
        @include('ticketmessage._form')
    </form>
@endsection