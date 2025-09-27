@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('ticket.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('ticket.entity') }}</h1>

    @php($ticket = new \App\Models\Ticket())

    <form method="POST" action="{{ route('ticket.store') }}" novalidate>
        @include('ticket._form')
    </form>
@endsection