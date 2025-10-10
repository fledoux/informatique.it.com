@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('company.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('company.entity') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('company.store') }}" novalidate>
                @include('company._form')
            </form>
        </div>
    </div>
@endsection