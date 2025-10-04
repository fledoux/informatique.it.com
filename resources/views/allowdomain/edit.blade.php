@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('allowdomain.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('allowdomain.entity') }}</h1>

    <form method="POST" action="{{ route('allowdomain.update', $allowDomainRegistration) }}" novalidate>
        @csrf
        @method('PUT')
        @include('allowdomain._form')
    </form>
@endsection