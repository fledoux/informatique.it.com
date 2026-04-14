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

                {{-- OIDC Login Button (Authentik SSO) --}}
                @if (config('oauth.client_id'))
                    <div class="d-grid gap-2 mb-3">
                        <a href="{{ route('oauth.redirect') }}" class="btn btn-outline-primary">
                            <i class="fas fa-shield-halved me-1"></i>
                            {{ __('login.Sign in with SSO') }}
                        </a>
                    </div>
                @endif

                {{-- Toggle Button for Traditional Login --}}
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-outline-primary" id="toggle-traditional-login">
                        <span id="toggle-text">{{ __('login.Traditional Login') }}</span>
                        <i class="fa-solid fa-chevron-down me-1"></i>
                    </button>
                </div>

                {{-- Email/Password Login Form (Hidden by default if SSO available) --}}
                <form method="POST" action="{{ route('login') }}" id="traditional-login-form"
                    @if (config('oauth.client_id')) style="display: none;" @endif>
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

                    @if (config('services.turnstile.site_key'))
                        <div class="my-3">
                            <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"
                                data-theme="{{ config('services.turnstile.theme', 'light') }}"
                                data-size="{{ config('services.turnstile.size', 'normal') }}"></div>
                            @error('cf-turnstile-response')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                    @endif

                    <button type="submit" class="btn btn-orange w-100 my-3">
                        {{ __('login.Connect') }}
                    </button>
                </form>

                {{-- Social Login Buttons --}}
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary" disabled title="Coming soon">
                        <i class="fa-brands fa-google me-1"></i>
                        Google <small>({{ __('login.Coming soon') }})</small>
                    </button>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary" disabled title="Coming soon">
                        <i class="fa-brands fa-apple me-1"></i>
                        Apple <small>({{ __('login.Coming soon') }})</small>
                    </button>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary" disabled title="Coming soon">
                        <i class="fa-brands fa-github me-1"></i>
                        GitHub <small>({{ __('login.Coming soon') }})</small>
                    </button>
                </div>

                @if (!config('oauth.client_id'))
                    {{-- If no SSO, show traditional login form directly --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-floating form-field-start">
                            <x-forms.input name="email" type="email" :label="__('login.Email')" :value="old('email')"
                                :required="true" :labelAfter="true" autofocus />
                        </div>
                        <div class="form-floating form-field-end">
                            <x-forms.input name="password" type="password" :label="__('login.Password')" :required="true"
                                :labelAfter="true" />
                        </div>
                        <x-forms.checkbox name="remember" :label="__('login.Remember me')" :checked="false" />

                        @if (config('services.turnstile.site_key'))
                            <div class="my-3">
                                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"
                                    data-theme="{{ config('services.turnstile.theme', 'light') }}"
                                    data-size="{{ config('services.turnstile.size', 'normal') }}"></div>
                                @error('cf-turnstile-response')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                        @endif

                        <button type="submit" class="btn btn-orange w-100 my-3">
                            {{ __('login.Connect') }}
                        </button>
                    </form>
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

    <script>
        @if (config('oauth.client_id'))
            document.getElementById('toggle-traditional-login').addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('traditional-login-form');
                const toggleText = document.getElementById('toggle-text');
                const icon = this.querySelector('i');

                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    toggleText.textContent = '{{ __('login.Hide Traditional Login') }}';
                    icon.className = 'fa-solid fa-chevron-up me-1';
                    // Focus on email field when showing
                    setTimeout(() => {
                        const emailInput = form.querySelector('input[name="email"]');
                        if (emailInput) emailInput.focus();
                    }, 100);
                } else {
                    form.style.display = 'none';
                    toggleText.textContent = '{{ __('login.Traditional Login') }}';
                    icon.className = 'fa-solid fa-chevron-down me-1';
                }
            });
        @endif
    </script>
@endsection
