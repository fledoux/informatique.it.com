@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('user.entity') }}</h1>

    @php($user = new \App\Models\User())

    <form method="POST" action="{{ route('user.store') }}" novalidate>
        @include('user._form')
    </form>
@endsection