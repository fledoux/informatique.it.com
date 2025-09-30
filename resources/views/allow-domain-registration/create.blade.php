@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('allow-domain-registration.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('allow-domain-registration.entity') }}</h1>

    @php($allowDomainRegistration = new \App\Models\AllowDomainRegistration())

    <form method="POST" action="{{ route('allow-domain-registration.store') }}" novalidate>
        @csrf
        @include('allow-domain-registration._form')
    </form>
@endsection