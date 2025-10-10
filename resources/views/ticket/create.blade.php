@extends('layouts.app')

@section('title', __('ticket.Create'))

@section('content')
    <h1 class="h3 mb-3">{{ __('ticket.Create') }}</h1>
    <form method="POST" action="{{ route('ticket.store') }}" novalidate>
        @include('ticket._form')
    </form>
@endsection
