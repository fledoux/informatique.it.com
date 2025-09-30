@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('allow-domain-registration.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('allow-domain-registration.entity') }}</h1>

    <form method="POST" action="{{ route('allow-domain-registration.update', $allowDomainRegistration) }}" novalidate>
        @csrf
        @method('PUT')
        @include('allow-domain-registration._form')
    </form>
@endsection