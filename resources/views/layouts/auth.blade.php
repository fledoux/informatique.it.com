<!DOCTYPE html>
<html class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.meta')
    @include('partials.favicon')
    <title>
        @yield('title', 'Bienvenue !')
    </title>
    <link rel="icon" href="{{ asset('img/favicon/favicon.svg') }}" type="image/svg+xml">
    @stack('stylesheets')
    @include('partials.style')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    @stack('javascripts')
    @include('partials.script')
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        @php($currentRoute = request()->route()->getName())
        @include('partials.flash')
        @yield('content')
    </main>
</body>
</html>