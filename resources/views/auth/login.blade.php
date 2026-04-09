@extends('layouts.auth')

@section('title', __('login.Connect'))

@section('content')
    <main class="form-signin w-100 m-auto">
        <div class="card rounded-2 shadow m-3">
            <div class="card-body p-2 p-sm-3">
                <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}"
                    alt="{{ config('app.brand_name') }} by Yellow Cactus" class="d-inline-block align-text-top mb-2 mx-3">
                <h5 class="card-title py-3">
                    <i class="fa-regular fa-shield-keyhole"></i>
                    {{ __('login.Please log in') }}
                </h5>

                {{-- Email/Password Login Form --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating form-field-start">
                        <x-forms.input name="email" type="email" :label="__('login.Email')" :value="old('email')" :required="true"
                            :labelAfter="true" autofocus />
                    </div>
                    <div class="form-floating form-field-end">
                        <x-forms.input name="password" type="password" :label="__('login.Password')" :required="true"
                            :labelAfter="true" />
                    </div>
                    <x-forms.checkbox name="remember" :label="__('login.Remember me')" :checked="false" />

                    <button type="submit" class="btn btn-lg btn-orange w-100 my-3">
                        {{ __('login.Connect') }}
                    </button>
                </form>

                {{-- OIDC Login Button (Synology SSO) --}}
                @if (config('oauth.client_id'))
                    <div class="text-center text-muted mb-3">
                        <span>{{ __('login.OR') }}</span>
                    </div>
                    <a href="{{ route('oauth.redirect') }}" class="btn btn-lg btn-primary w-100 mb-3">
                        <i class="fa-solid fa-key me-2"></i>
                        {{ __('login.Sign in with SSO') }}
                    </a>
                @endif
                <div class="mb-2">
                    <a href="{{ route('register') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-user-plus me-1"></i>
                        {{ __('global.Register') }}
                    </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('password.request') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-key me-1"></i>
                        {{ __('login.Forgot password') }}
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
