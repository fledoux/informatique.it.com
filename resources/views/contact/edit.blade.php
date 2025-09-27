@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('contact.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('contact.entity') }}</h1>

    <form method="POST" action="{{ route('contact.update', $contact) }}" novalidate>
        @csrf @method('PUT')
        @include('contact._form')
    </form>
@endsection