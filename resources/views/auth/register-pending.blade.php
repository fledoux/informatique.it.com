@extends('layouts.auth')

@section('title', __('Register.Register'))

@section('content')
<div class="alert alert-primary" role="alert">
    <h4 class="alert-heading">{{ __('Register.Check your email') }}</h4>
    <p class="m-0">{{ __('Register.We have just sent you a link to complete your registration.') }}</p>
</div>
@endsection