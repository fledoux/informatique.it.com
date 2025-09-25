@extends('layouts.app')

@section('title', __('global.Edit') . ' — ' . __('ticket.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!} — {{ __('ticket.entity') }}</h1>

    <form method="POST" action="{{ route('ticket.update', $ticket) }}" novalidate>
        @csrf @method('PUT')
        @include('ticket._form')
    </form>
@endsection