{{-- HERO --}}
<header class="hero mt-0 pt-4 mb-5 pb-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge badge-soft rounded-pill mb-3">
                    <i class="fa-solid fa-shield-halved me-1"></i>
                    Assistance rapide — Distant & Sur site</span>
                <h1 class="display-5 fw-bold mb-3">
                    Support informatique
                    <span class="text-orange">depuis plus de {{ date('Y') - 2000 }} ans</span>
                    pour
                    <br class="d-none d-lg-inline">entreprises et particuliers
                </h1>
                <p class="lead text-secondary mb-4">
                    Dépannage express, maintenance proactive, cybersécurité et infogérance. Nous prenons en charge vos incidents et prévenons les suivants.
                    <u>Plus de
                        <span class="support">9999</span>
                        demandes traitées</u>
                    depuis l’an 2000.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#contact" class="btn btn-orange btn-lg w-100 w-sm-auto">
                        <i class="fa-regular fa-calendar-check me-2"></i><span class="d-none d-sm-inline">Obtenir un&nbsp;</span>devis en&nbsp;2&nbsp;min
                    </a>
                    <a href="#tarifs" class="btn btn-outline-orange btn-lg w-100 w-sm-auto">
                        <i class="fa-regular fa-tags me-2"></i>Voir les tarifs
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3 mt-4">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-star text-warning"></i>
                        <i class="fa-solid fa-star text-warning"></i>
                        <i class="fa-solid fa-star text-warning"></i>
                        <i class="fa-solid fa-star text-warning"></i>
                        <i class="fa-solid fa-star-half-stroke text-warning"></i>
                    </div>
                    <small class="text-muted">4.8/5 — 370+ interventions notées</small>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 p-lg-5 bg-white rounded-4 border shadow-soft">
                    <h3 class="fw-bold mb-3">
                        <i class="fa-regular fa-screwdriver-wrench me-2 text-orange"></i>Besoin d’aide maintenant ?
                    </h3>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fa-solid fa-circle-check check me-2"></i>PC/Mac lents, virus, écran noir
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-circle-check check me-2"></i>Messagerie, imprimantes, Wi-Fi, réseaux
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-circle-check check me-2"></i>Microsoft 365/Google Workspace, sauvegardes
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-circle-check check me-2"></i>Cybersécurité, MFA, pare-feu, anti-phishing
                        </li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="btn btn-orange w-100">
                        <i class="fa-solid fa-headset me-2"></i>Parler à un technicien</a>
                    <div class="small text-muted mt-2 mb-0">
                        <p class="mb-1">
                            <i class="fa-regular fa-clock me-1 text-orange"></i>
                            Intervention sous 24h ouvrées
                        </p>
                        <p class="mb-1">
                            <i class="fa-regular fa-globe me-1 text-orange"></i>
                            À distance partout en France
                        </p>
                        <p class="mb-0">
                            <i class="fa-regular fa-map-marker-alt me-1 text-orange"></i>
                            Sur site en région
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <div class="logo-scroller" aria-label="Ils nous font confiance">
                    <div class="logo-track">
                        <img src="{{ asset('assets/img/customer/sanofi.svg') }}" alt="Sanofi" />
                        <img src="{{ asset('assets/img/customer/samsung.svg') }}" alt="Samsung" />
                        <img src="{{ asset('assets/img/customer/total.svg') }}" alt="TotalEnergies" />
                        <img src="{{ asset('assets/img/customer/natixis.png') }}" alt="Natixis Investment Managers" />
                        <img src="{{ asset('assets/img/customer/hopscotch.png') }}" class="bg-black p-2" alt="Hopscotch Groupe" />
                        <img src="{{ asset('assets/img/customer/ls.svg') }}" alt="LS GROUP" />
                        <img src="{{ asset('assets/img/customer/roche.svg') }}" alt="Roche" />
                        <img src="{{ asset('assets/img/customer/neutrik.svg') }}" alt="Neutrik France" />
                        <img src="{{ asset('assets/img/customer/sagarmatha.svg') }}" alt="Sagarmatha" />
                        <img src="{{ asset('assets/img/customer/pwc.svg') }}" alt="PricewaterhouseCoopers" />
                        <!-- duplicate for seamless scroll -->
                        <img src="{{ asset('assets/img/customer/sanofi.svg') }}" alt="Sanofi" />
                        <img src="{{ asset('assets/img/customer/samsung.svg') }}" alt="Samsung" />
                        <img src="{{ asset('assets/img/customer/total.svg') }}" alt="TotalEnergies" />
                        <img src="{{ asset('assets/img/customer/natixis.png') }}" alt="Natixis Investment Managers" />
                        <img src="{{ asset('assets/img/customer/hopscotch.png') }}" class="bg-black p-2" alt="Hopscotch Groupe" aria-hidden="true" />
                        <img src="{{ asset('assets/img/customer/ls.svg') }}" alt="LS GROUP" aria-hidden="true" />
                        <img src="{{ asset('assets/img/customer/roche.svg') }}" alt="Roche" aria-hidden="true" />
                        <img src="{{ asset('assets/img/customer/neutrik.svg') }}" alt="Neutrik France" aria-hidden="true" />
                        <img src="{{ asset('assets/img/customer/sagarmatha.svg') }}" alt="Sagarmatha" aria-hidden="true" />
                        <img src="{{ asset('assets/img/customer/pwc.svg') }}" alt="PricewaterhouseCoopers" aria-hidden="true" />
                    </div>
                </div>
            </div>
        </div>
</header>
<script src="{{ asset('assets/js/logo-scroller.js') }}" defer></script>