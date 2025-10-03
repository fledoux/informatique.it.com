@extends('layouts.public')

@section('title')
    @if(isset($exception) && $exception->getMessage())
        {{ __('error.' . $exception->getMessage()) }}
    @else
        {{ __('error.403.title') }}
    @endif
@endsection

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center">
        <div class="col-12">
            <div class="text-center">
                <!-- Icône d'erreur -->
                <div class="mb-4">
					<i class="fa-regular fa-shield-exclamation display-1 text-danger"></i>
                </div>
                
                <!-- Code d'erreur -->
                <h1 class="display-1 fw-bold text-danger mb-0">403</h1>
                <h2 class="h4 fw-normal text-secondary mb-4">
                    @if(isset($exception) && $exception->getMessage())
                        {{ __('error.' . $exception->getMessage()) }}
                    @else
                        {{ __('error.403.title') }}
                    @endif
                </h2>
                
                <!-- Message d'explication -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-6">
                        <p class="text-secondary">
                            {{ __('error.403.message') }}
                        </p>
                        <p class="text-secondary small">
                            {{ __('error.403.help') }}
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-5">
                    <a href="{{ route('home') }}" class="btn btn-orange">
                        <i class="fa-regular fa-home me-2"></i>
                        {{ __('global.Back to home') }}
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-orange">
                            <i class="fa-regular fa-tachometer-alt me-2"></i>
                            {{ __('global.Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-orange">
                            <i class="fa-regular fa-sign-in me-2"></i>
                            {{ __('global.Login') }}
                        </a>
                    @endauth
                    
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="fa-regular fa-arrow-left me-2"></i>
                        {{ __('global.Go back') }}
                    </button>
                </div>
                
                <!-- Informations sur les permissions -->
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card border-0 bg-light mx-auto">
                            <div class="card-body border py-4 text-start">
                                <h6 class="card-title fw-semibold mb-3 text-center">
                                    <i class="fa-regular fa-info-circle me-1 text-primary"></i>
                                    {{ __('global.About permissions') }} :
                                </h6>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <p class="text-secondary small mb-2">
                                            <i class="fa-regular fa-user-shield text-primary me-2"></i>
                                            {{ __('global.This resource requires specific permissions to access') }}
                                        </p>
                                        <p class="text-secondary small mb-2">
                                            <i class="fa-regular fa-users text-primary me-2"></i>
                                            {{ __('global.Contact your team administrator to request access') }}
                                        </p>
                                        <p class="text-secondary small mb-0">
                                            <i class="fa-regular fa-envelope text-primary me-2"></i>
                                            {{ __('global.For technical support') }} : 
                                            <a href="mailto:{{ config('app.company.emails.dev') }}" class="text-decoration-none">
                                                {{ config('app.company.emails.dev') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection