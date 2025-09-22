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
                <x-forms.input 
                    name="email" 
                    type="email" 
                    :label="__('Register.Email')"
                    :value="old('email')"
                    :required="true"
                    :labelAfter="true"
                    autofocus />
            </div>

            {{-- Password field with floating label --}}
            <div class="form-floating mb-3">
                <x-forms.input 
                    name="password" 
                    type="password" 
                    :label="__('Register.Password')"
                    :required="true"
                    :labelAfter="true" />
            </div>

            {{-- Password confirmation field with floating label --}}
            <div class="form-floating mb-3">
                <x-forms.input 
                    name="password_confirmation" 
                    type="password" 
                    :label="__('Register.Confirm Password')"
                    :required="true"
                    :labelAfter="true" />
            </div>

            {{-- Terms agreement checkbox --}}
            <x-forms.checkbox 
                name="agree_terms" 
                :label="__('Register.Agree terms')" 
                :checked="false"
                :required="true" />

            <button type="submit" class="btn btn-orange w-100 my-3">
                {{ __('Register.Register') }}
            </button>
        </form>
        
        <p>
            <a href="{{ route('home') }}" class="btn btn-link p-0 float-end text-secondary">
                {{ __('global.Cancel') }}
            </a>
        </p>
    </div>
</div>
@endsection