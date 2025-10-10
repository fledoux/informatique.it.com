@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('allowdomain.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('allowdomain.h1.Create') !!}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('allowdomain.store') }}" novalidate>
                @csrf
                @include('allowdomain._form')
            </form>
        </div>
    </div>
@endsection