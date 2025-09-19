@extends('layouts.auth')

@section('title', __('Register.Register'))

@section('content')
@if(session('verify_email_error'))
    <div class="alert alert-danger" role="alert">
        {{ session('verify_email_error') }}
    </div>
@endif

<div class="card rounded-4 shadow m-3">
    <div class="card-body">
        <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="Mon Support by Yellow Cactus" class="d-inline-block align-text-top mb-2 mx-3">
        <h5 class="card-title py-3">
            <i class="fa-regular fa-address-card"></i>
            {{ __('Register.Create an account') }}
        </h5>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            {{-- Email field with floating label --}}
            <div class="form-floating mb-3">
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder=" " 
                       required 
                       autofocus>
                <label for="email">{{ __('Register.Email') }}</label>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password field with floating label --}}
            <div class="form-floating mb-3">
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder=" " 
                       required>
                <label for="password">{{ __('Register.Password') }}</label>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password confirmation field with floating label --}}
            <div class="form-floating mb-3">
                <input type="password" 
                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder=" " 
                       required>
                <label for="password_confirmation">{{ __('Register.Confirm Password') }}</label>
                @error('password_confirmation')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Terms agreement checkbox --}}
            <div class="form-check p-0 mb-3">
                <input type="checkbox" 
                       class="form-check-input @error('agree_terms') is-invalid @enderror" 
                       id="agree_terms" 
                       name="agree_terms" 
                       required>
                <label class="form-check-label" for="agree_terms">
                    {{ __('Register.Agree terms') }}
                </label>
                @error('agree_terms')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-orange w-100 my-3">
                {{ __('Register.Register') }}
            </button>
        </form>
        
        <p>
            <a href="{{ route('home') }}" class="btn btn-link p-0 float-end text-secondary">
                {{ __('Global.Cancel') }}
            </a>
        </p>
    </div>
</div>
@endsection