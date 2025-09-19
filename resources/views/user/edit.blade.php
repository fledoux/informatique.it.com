@extends('layouts.app')

@section('title', __('crud.Edit') . ' — ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('crud.Edit') }} — {{ __('user.entity') }}</h1>

    <form method="POST" action="{{ route('user.update', $user) }}" novalidate>
        @csrf @method('PUT')
        @include('user._form')
    </form>
@endsection