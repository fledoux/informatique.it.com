<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mon Application')</title>

    <!-- Bootstrap 5 depuis CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tes styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">MonSite</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}">Déconnexion</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="container flex-grow-1">
        @yield('content')
    </main>

    <!-- Pied de page -->
    <footer class="bg-light text-center py-3 mt-4">
        <small>&copy; {{ date('Y') }} MonSite - Tous droits réservés.</small>
    </footer>

    <!-- Bootstrap JS depuis CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Ton JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>