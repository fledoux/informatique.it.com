@extends('layouts.app')

@section('title', __('crud.Create') . ' — ' . __('company.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('crud.Create') }} — {{ __('company.entity') }}</h1>

    @php($company = new \App\Models\Company())

    <form method="POST" action="{{ route('company.store') }}" novalidate>
        @include('company._form')
    </form>
@endsection