@extends('layouts.auth')

@section('title', __('passwords.Reset Password'))

@section('content')
    <main class="form-signin w-100 m-auto">
        <div class="card rounded-4 shadow m-3">
            <div class="card-body">
                <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}" alt="{{ config('app.brand_name') }} by Yellow Cactus"
                    class="d-inline-block align-text-top mb-2 mx-3">
                <h5 class="card-title py-3">
                    <i class="fa-regular fa-key"></i>
                    {{ __('passwords.Reset Password') }}
                </h5>
                <p class="text-secondary small mb-4">
                    {{ __('passwords.Enter your email address and we will send you a link to reset your password') }}
                </p>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {!! session('status') !!}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-floating">
                        <x-forms.input name="email" type="email" :label="__('login.Email')" :value="old('email')" :required="true"
                            :labelAfter="true" autofocus />
                    </div>

                    <button type="submit" class="btn btn-orange w-100 my-3">
                        {{ __('passwords.Send Password Reset Link') }}
                    </button>
                </form>
                <div class="mb-2">
                    <a href="{{ route('login') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-arrow-left me-1"></i>
                        {{ __('passwords.Back to Login') }}
                    </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('register') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-user-plus me-1"></i>
                        {{ __('global.Register') }}
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
    </main>
@endsection
