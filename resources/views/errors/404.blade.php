@extends('layouts.public')

@section('title', __('global.Page not found'))

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center">
        <div class="col-12">
            <div class="text-center">
                <!-- Icône d'erreur -->
                <div class="mb-4">
					<i class="fa-regular fa-triangle-exclamation display-1 text-warning"></i>
                </div>
                
                <!-- Code d'erreur -->
                <h1 class="display-1 fw-bold text-primary mb-0">404</h1>
                <h2 class="h4 fw-normal text-secondary mb-4">{{ __('global.Page not found') }}</h2>
                
                <!-- Message d'explication -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-6">
                        <p class="text-secondary">
                            {{ __('global.The page you are looking for does not exist or has been moved') }}
                        </p>
                        <p class="text-secondary small">
                            {{ __('global.Please check the URL or use the navigation to find what you are looking for') }}
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-5">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fa-regular fa-home me-2"></i>
                        {{ __('global.Back to home') }}
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fa-regular fa-tachometer-alt me-2"></i>
                            {{ __('global.Dashboard') }}
                        </a>
                    @endauth
                    
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="fa-regular fa-arrow-left me-2"></i>
                        {{ __('global.Go back') }}
                    </button>
                </div>
                
                <!-- Liens utiles -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-4">
                                <h6 class="card-title fw-semibold mb-3">
                                    <i class="fa-regular fa-lightbulb me-2 text-warning"></i>
                                    {{ __('global.You might be interested in') }} :
                                </h6>
                                
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <a href="{{ route('home') }}" class="btn btn-link text-decoration-none p-2 w-100">
                                            <i class="fa-regular fa-home text-primary me-2"></i>
                                            {{ __('nav.Home') }}
                                        </a>
                                    </div>
                                    
                                    @guest
                                        <div class="col-md-4">
                                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none p-2 w-100">
                                                <i class="fa-regular fa-sign-in text-primary me-2"></i>
                                                {{ __('nav.Login') }}
                                            </a>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <a href="{{ route('register') }}" class="btn btn-link text-decoration-none p-2 w-100">
                                                <i class="fa-regular fa-user-plus text-primary me-2"></i>
                                                {{ __('nav.Register') }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none p-2 w-100">
                                                <i class="fa-regular fa-tachometer-alt text-primary me-2"></i>
                                                {{ __('nav.Dashboard') }}
                                            </a>
                                        </div>
                                        
                                        @can('ticket.index')
                                            <div class="col-md-4">
                                                <a href="{{ route('ticket.index') }}" class="btn btn-link text-decoration-none p-2 w-100">
                                                    <i class="fa-regular fa-ticket text-primary me-2"></i>
                                                    {{ __('nav.Tickets') }}
                                                </a>
                                            </div>
                                        @endcan
                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact support -->
                <div class="mt-5 pt-4 border-top">
                    <p class="text-secondary small">
                        {{ __('global.Still having trouble?') }}
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

@section('scripts')
<script>
{{-- Animation d'entrée --}}
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('.fa-face-sad-tear, .display-1, .h4, .text-secondary, .btn, .card');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        setTimeout(() => {
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection