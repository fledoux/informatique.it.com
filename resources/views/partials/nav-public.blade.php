<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="informatique.it.com" class="brand-logo d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navMain" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#offres">Offres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tarifs">Tarifs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cas-clients">Cas clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#faq">FAQ</a>
                </li>
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="btn btn-orange w-100" href="#contact">
                        <i class="fa-solid fa-bolt me-1"></i>
                        Devis gratuit</a>
                </li>
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="ms-auto btn btn-outline-orange w-100" href="{{ route('register') }}">
                        <i class="fa-regular fa-address-card"></i>
                        {{ __('Nav.Register') }}
                    </a>
                </li>
                <li class="nav-item ms-lg-2 mb-2">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('login') }}">
                        <i class="fa-regular fa-arrow-right-to-bracket"></i>
                        {{ __('Nav.Connect') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>