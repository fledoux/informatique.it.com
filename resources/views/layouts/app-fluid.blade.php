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
    @include('partials.script')
</head>
<body class="d-flex flex-column h-100 bg-body-tertiary">
    @php($currentRoute = request()->route() ? request()->route()->getName() : null)
    @include('partials.nav')
    @include('partials._impersonat')
    <div class="container-fluid pb-5">
        <main class="flex-shrink-0">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
    @include('partials.footer')
    @include('partials._matomo')
    @include('partials._scrolltop')
    @stack('javascripts')
</body>
</html>