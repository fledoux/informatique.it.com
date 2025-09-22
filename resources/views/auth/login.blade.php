@extends('layouts.auth')

@section('title', __('login.Connect'))

@section('content')
    <main class="form-signin w-100 m-auto">
        <div class="card rounded-4 shadow m-3">
            <div class="card-body">
                <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="Mon Support by Yellow Cactus"
                    class="d-inline-block align-text-top mb-2 mx-3">
                <h5 class="card-title py-3">
                    <i class="fa-regular fa-shield-keyhole"></i>
                    {{ __('login.Please log in') }}
                </h5>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating">
                        <x-forms.input 
                            name="email" 
                            type="email" 
                            :label="__('login.Email')"
                            :value="old('email')"
                            :required="true"
                            :labelAfter="true"
                            autofocus />
                    </div>

                    <div class="form-floating">
                        <x-forms.input 
                            name="password" 
                            type="password"
                            :label="__('login.Password')"
                            :required="true"
                            :labelAfter="true" />
                    </div>

                    <x-forms.checkbox 
                        name="remember" 
                        :label="__('login.Remember me')" 
                        :checked="false" />

                    <button type="submit" class="btn btn-orange w-100 my-3">
                        {{ __('login.Connect') }}
                    </button>
                </form>

                <p>
                    <a href="{{ route('home') }}" class="btn btn-link p-0 float-end text-secondary">
                        {{ __('global.Cancel') }}
                    </a>
                </p>
            </div>
        </div>
    </main>
@endsection
