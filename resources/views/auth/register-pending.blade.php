@extends('layouts.auth')

@section('title', __('register.Register'))

@section('content')
    @if (session('verify_email_error'))
        <div class="alert alert-danger" role="alert">
            {{ session('verify_email_error') }}
        </div>
    @endif
    <div class="card rounded-2 shadow m-3">
        <div class="card-body p-2 p-sm-3">
            <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}" alt="{{ config('app.brand_name') }} by Yellow Cactus"
                class="d-inline-block align-text-top mb-2 mx-3">
            <h5 class="card-title py-3">
                <i class="fa-regular fa-address-card"></i>
                {{ __('register.Create an account') }}
            </h5>
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">{{ __('register.Check your email') }}</h4>
                <p class="m-0">{!! __('register.We have just sent you a link to complete your registration.') !!}</p>
            </div>
            <div class="mb-2">
                <a href="{{ route('login') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                    <i class="fa-regular fa-arrow-left me-1"></i>
                    {{ __('passwords.Back to Login') }}
                </a>
            </div>
            <div>
                <a href="{{ route('home') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                    <i class="fa-regular fa-rotate-left me-1"></i>
                    {{ __('global.Cancel') }}
                </a>
            </div>
        </div>
    </div>
    @include('emails._baseline-small')
@endsection
