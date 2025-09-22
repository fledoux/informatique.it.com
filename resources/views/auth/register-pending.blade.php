@extends('layouts.auth')

@section('title', __('register.Register'))

@section('content')
<div class="alert alert-primary" role="alert">
    <h4 class="alert-heading">{{ __('register.Check your email') }}</h4>
    <p class="m-0">{{ __('register.We have just sent you a link to complete your registration.') }}</p>
</div>
@endsection