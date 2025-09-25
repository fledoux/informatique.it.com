@extends('layouts.public')

@section('title', __('global.Server error'))

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center">
        <div class="col-12">
            <div class="text-center">
                <!-- Icône d'erreur -->
                <div class="mb-4">
					<i class="fa-regular fa-bomb display-1 text-danger"></i>
                </div>
                
                <!-- Code d'erreur -->
                <h1 class="display-1 fw-bold text-danger mb-0">500</h1>
                <h2 class="h4 fw-normal text-secondary mb-4">{{ __('global.Server error') }}</h2>
                
                <!-- Message d'explication -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-6">
                        <p class="text-secondary">
                            {{ __('global.Something went wrong on our servers') }}
                        </p>
                        <p class="text-secondary small">
                            {{ __('global.Our team has been notified and is working to fix this issue') }}
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-5">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fa-regular fa-home me-2"></i>
                        {{ __('global.Back to home') }}
                    </a>
                    
                    <button onclick="location.reload()" class="btn btn-outline-primary">
                        <i class="fa-regular fa-refresh me-2"></i>
                        {{ __('global.Try again') }}
                    </button>
                    
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="fa-regular fa-arrow-left me-2"></i>
                        {{ __('global.Go back') }}
                    </button>
                </div>
                
                <!-- Contact support -->
                <div class="mt-5 pt-4 border-top">
                    <p class="text-secondary small">
                        {{ __('global.If the problem persists, please') }}
                        <a href="mailto:support@informatique.it" class="text-decoration-none">
                            <i class="fa-regular fa-envelope me-1"></i>
                            {{ __('global.Contact support') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection