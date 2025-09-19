@extends('layouts.app')

@section('title', __('crud.Create') . ' — ' . __('contact.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('crud.Create') }} — {{ __('contact.entity') }}</h1>

    @php($contact = new \App\Models\Contact())

    <form method="POST" action="{{ route('contact.store') }}" novalidate>
        @include('contact._form')
    </form>
@endsection