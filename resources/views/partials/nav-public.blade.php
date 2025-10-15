{{-- NAVBAR --}}

<nav class="navbar border-top border-5 border-orange navbar-expand-lg bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="{{ config('app.brand_name') }}"
                class="brand-logo d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navMain" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#offres">{{ __('nav.Offers') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#tarifs">{{ __('nav.Pricing') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#cas-clients">{{ __('nav.CaseStudies') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#faq">{{ __('nav.FAQ') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('qr-code') }}">
                        <i class="fa-solid fa-qrcode me-1"></i>{{ __('nav.Scan') }}
                    </a>
                </li>
                @include('partials._lang')
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="btn btn-orange w-100" href="{{ route('home') }}#contact">
                        <i class="fa-solid fa-bolt me-1"></i>
                        {{ __('nav.FreeQuote') }}</a>
                </li>
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="ms-auto btn btn-outline-orange w-100" href="{{ route('register') }}">
                        <i class="fa-regular fa-address-card"></i>
                        {{ __('nav.Register') }}
                    </a>
                </li>
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('login') }}">
                        <i class="fa-regular fa-arrow-right-to-bracket"></i>
                        {{ __('nav.Connect') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
