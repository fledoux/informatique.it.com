@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('ticket_message.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('ticket_message.entity') }}</h1>

    <form method="POST" action="{{ route('ticket_message.update', $ticketMessage) }}" novalidate>
        @csrf
        @method('PUT')
        @include('ticket_message._form')
    </form>
@endsection