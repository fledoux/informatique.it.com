@extends('layouts.app')

@section('title', __('global.Edit') . ' — ' . __('company.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Edit') }} — {{ __('company.entity') }}</h1>

    <form method="POST" action="{{ route('company.update', $company) }}" novalidate>
        @csrf @method('PUT')
        @include('company._form')
    </form>
@endsection