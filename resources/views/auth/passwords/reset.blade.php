@extends('layouts.auth')

@section('title', __('passwords.Reset Password'))

@section('content')
    <main class="form-signin w-100 m-auto">
        <div class="card rounded-4 shadow m-3">
            <div class="card-body">
                <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}" alt="Mon Support by Yellow Cactus"
                    class="d-inline-block align-text-top mb-2 mx-3">
                <h5 class="card-title py-3">
                    <i class="fa-regular fa-lock-keyhole"></i>
                    {{ __('passwords.Reset Password') }}
                </h5>
                
                <p class="text-secondary small mb-4">
                    {{ __('passwords.Enter your new password') }}
                </p>

                <!-- Affichage des règles de mot de passe -->
                <div class="alert alert-info small mb-4">
                    <h6 class="mb-2">
                        <i class="fa-regular fa-info-circle me-1"></i>
                        {{ __('passwords.Password Requirements') }} :
                    </h6>
                    <ul class="mb-0 ps-3">
                        @foreach(App\Helpers\Helper::getPasswordRules() as $rule)
                            <li>{{ $rule }}</li>
                        @endforeach
                    </ul>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="form-floating form-field-start">
                        <x-forms.input name="email" type="email" :label="__('login.Email')" :value="$email ?? old('email')" :required="true"
                            :labelAfter="true" readonly />
                    </div>
                    
                    <div class="form-floating form-field-middle">
                        <x-forms.input name="password" type="password" :label="__('passwords.New Password')" :required="true"
                            :labelAfter="true" autofocus />
                    </div>
                    
                    <div class="form-floating form-field-end">
                        <x-forms.input name="password_confirmation" type="password" :label="__('passwords.Confirm Password')" :required="true"
                            :labelAfter="true" />
                    </div>

                    <button type="submit" class="btn btn-orange w-100 my-3">
                        {{ __('passwords.Reset Password') }}
                    </button>
                </form>
                
                <div class="mb-2">
                    <a href="{{ route('login') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-arrow-left me-1"></i>
                        {{ __('passwords.Back to Login') }}
                    </a>
                </div>
                <div class="mb-2">
                    <a href="{{ route('home') }}" class="btn btn-link p-0 text-secondary text-decoration-none">
                        <i class="fa-regular fa-rotate-left me-1"></i>
                        {{ __('global.Cancel') }}
                    </a>
                </div>
            </div>
        </div>
		@include('emails._baseline2')
    </main>
@endsection