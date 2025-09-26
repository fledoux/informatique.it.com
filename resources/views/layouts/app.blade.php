<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-bs-theme="light" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.meta')
    @include('partials.favicon')
    <title>
        @yield('title', 'Support informatique')
    </title>
    <link rel="icon" href="{{ asset('assets/img/favicon/favicon.svg') }}" type="image/svg+xml">
    @stack('stylesheets')
    @include('partials.style')
    @stack('javascripts')
    @include('partials.script')
</head>
<body class="d-flex flex-column h-100 bg-body-tertiary">
    @php($currentRoute = request()->route() ? request()->route()->getName() : null)
    @include('partials.nav')
    
    {{-- Indicateur d'impersonation --}}
    @if(session('impersonating_from'))
        <div class="alert alert-warning mb-0 text-center border-0 rounded-0">
            <i class="fa-regular fa-user-gear me-2"></i>
            Vous êtes connecté en tant que <strong>{{ auth()->user()->name }}</strong>
            <form method="POST" action="{{ route('user.stop-impersonation') }}" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fa-regular fa-sign-out me-1"></i> Retour admin
                </button>
            </form>
        </div>
    @endif
    
    <div class="container pb-5">
        <main class="flex-shrink-0">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
    @include('partials.footer')
    @include('partials._matomo')
</body>
</html>