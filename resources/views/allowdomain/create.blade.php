@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('allowdomain.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('allowdomain.entity') }}</h1>

    @php($allowDomainRegistration = new \App\Models\AllowDomainRegistration())

    <form method="POST" action="{{ route('allowdomain.store') }}" novalidate>
        @csrf
        @include('allowdomain._form')
    </form>
@endsection