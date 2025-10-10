@extends('layouts.app')

@section('title', __('user.Create') . '  ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('user.h1.Create') !!}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('user.store') }}" novalidate>
                @include('user._form')
            </form>
        </div>
    </div>
@endsection