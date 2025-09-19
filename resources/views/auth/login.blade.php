@extends('layouts.auth')

@section('title', __('Login.Connect'))

@section('content')
<div class="card rounded-4 shadow m-3">
    <div class="card-body">
        <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="Mon Support by Yellow Cactus" class="d-inline-block align-text-top mb-2 mx-3">
        <h5 class="card-title py-3">
            <i class="fa-regular fa-shield-keyhole"></i>
            {{ __('Login.Please log in') }}
        </h5>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Login.Email') }}</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Login.Password') }}</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    {{ __('Login.Remember me') }}
                </label>
            </div>

            <button type="submit" class="btn btn-orange w-100 my-3">
                {{ __('Login.Connect') }}
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