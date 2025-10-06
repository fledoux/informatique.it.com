@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('ticketmessage.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('ticketmessage.entity') }}</h1>

    <form method="POST" action="{{ route('ticketmessage.update', $ticketMessage) }}" novalidate>
        @csrf
        @method('PUT')
        @include('ticketmessage._form')
    </form>
@endsection