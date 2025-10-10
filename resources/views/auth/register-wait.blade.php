@extends('layouts.auth')

@section('title', __('register.Register'))

@section('content')
<div class="card rounded-4 shadow m-3">
    <div class="card-body p-2 p-sm-3">
        <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}" alt="{{ config('app.brand_name') }} by Yellow Cactus" class="d-inline-block align-text-top mb-2 mx-3">
        <h5 class="card-title py-3">
            <div class="alert alert-warning text-center">
                <strong>{{ __('register.Opening') }}</strong> : <span class="text-orange">{!! __('register.Opening date') !!}</span>
            </div>
        </h5>
        <p>{{ __('register.If you need support now') }}</p>
        <p class="text-center">{{ __('register.Contact us at') }}&nbsp;<strong>01&nbsp;49&nbsp;66&nbsp;21&nbsp;77</strong></p>
        <p class="text-center">{!! __('register.Or visit') !!}</p>
        <p class="text-center">
            <a href="https://extranet.yellowcactus.com" class="text-orange" target="_blank">extranet.yellowcactus.com</a>
        </p>

        <p>
            <a href="{{ route('home') }}" class="btn btn-link p-0 float-end text-secondary text-decoration-none">
                {!! __('global.btn.Back') !!}
            </a>
        </p>
    </div>
</div>
@endsection
