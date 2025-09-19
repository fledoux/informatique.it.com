<!DOCTYPE html>
<html lang="fr" data-bs-theme="light" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.meta')
    @include('partials.favicon')
    <title>
        @yield('title', 'Bienvenue !')
    </title>
    
    @stack('stylesheets')
    @include('partials.style')
    @stack('javascripts')
    @include('partials.script')
</head>
<body class="body d-flex flex-column h-100 bg-body-tertiary">
    @php($currentRoute = request()->route()->getName())
    @include('partials.nav-public')
    <div class="container">
        <main class="flex-shrink-0">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
    @include('partials.footer')
</body>
</html>