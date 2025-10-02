@extends('layouts.app')

@section('title', __('user.Create') . '  ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('user.Create') }}</h1>

    @php($user = new \App\Models\User())

    <form method="POST" action="{{ route('user.store') }}" novalidate>
        @include('user._form')
    </form>
@endsection