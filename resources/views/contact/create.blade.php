@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('contact.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('contact.h1.Create') !!}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('contact.store') }}" novalidate>
                @include('contact._form')
            </form>
        </div>
    </div>
@endsection