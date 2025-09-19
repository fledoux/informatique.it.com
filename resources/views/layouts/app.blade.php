<!DOCTYPE html>
<html lang="fr" data-bs-theme="light" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.meta')
    @include('partials.favicon')
    <title>
        @yield('title', 'Support informatique')
    </title>
    <link rel="icon" href="{{ asset('img/favicon/favicon.svg') }}" type="image/svg+xml">
    @stack('stylesheets')
    @include('partials.style')
    @stack('javascripts')
    @include('partials.script')
</head>
<body class="d-flex flex-column h-100 bg-body-tertiary">
    @php($currentRoute = request()->route()->getName())
    @include('partials.nav')
    <div class="container pb-5">
        <main class="flex-shrink-0">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
    @include('partials.footer')
</body>
</html>