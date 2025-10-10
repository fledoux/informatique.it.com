@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.btn.Edit') !!} {{ $user->firstname ? $user->firstname : __('user.entity') }} {{ $user->lastname ? $user->lastname : '' }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('user.update', $user) }}" novalidate>
                @csrf @method('PUT')
                @include('user._form')
            </form>
        </div>
    </div>
@endsection