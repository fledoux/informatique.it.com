{{-- TARIFS --}}
<section id="tarifs" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Tarifs transparents</h2>
        <div class="gradient-bar"></div>

        <div class="row g-4 mt-4 align-items-stretch">
            <div class="col-md-12 col-lg-6">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div class="me-md-3">
                        <h6 class="text-muted mb-1">Achat de Ticket unitaire</h6>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="price mb-0">
                                <span class="js-price" data-ht="68">68</span>€
                            </h3>
                            <span class="text-secondary small" data-suffix="ticket">/ ticket H.T. (<span class="fw-bold">30 min</span>), valable 1 an.</span>
                        </div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100 w-sm-auto mt-3 mt-md-0">Souscrire</a>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="p-4 bg-white bg-opacity-25 border rounded-4 h-100 d-flex flex-column justify-content-center gap-2">
                    <div class="form-check form-switch form-switch-orange form-switch-lg m-0">
                        <input class="form-check-input" type="checkbox" id="toggleTTC">
                        <label class="form-check-label fw-bold text-orange" for="toggleTTC">Affichage des Tarifs en TTC</label>
                    </div>
                    <p class="text-secondary mb-0">
                        Tarifs au 1<sup>er</sup>
                        septembre
                        {{ "now"|date("Y") }}<br>1 ticket équivant à une intervention de 30 minutes.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100">
                    <h6 class="text-muted">Pack 10 tickets</h6>
                    <h3 class="price mb-0">
                        <span class="js-price" data-ht="570">570</span>€
                    </h3>
                    <p class="text-secondary" data-suffix="pack">/ pack H.T.</p>
                    <ul class="list-unstyled small">
                        <li class="mb-2 fw-bold">
                            <i class="fa-regular fa-check text-success me-2"></i>10 x 30 minutes
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>
                            <span class="js-per" data-ht="57">57</span>€ / ticket
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>Validité 1 an
                        </li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100">Souscrire</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100">
                    <h6 class="text-muted">Pack 50 tickets</h6>
                    <h3 class="price mb-0">
                        <span class="js-price" data-ht="2300">2 300</span>€
                    </h3>
                    <p class="text-secondary" data-suffix="pack">/ pack H.T.</p>
                    <ul class="list-unstyled small">
                        <li class="mb-2 fw-bold">
                            <i class="fa-regular fa-check text-success me-2"></i>50 x 30 minutes
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>
                            <span class="js-per" data-ht="46">46</span>€ / ticket
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>Validité 2 ans
                        </li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100">Souscrire</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 position-relative">
                    <span class="ribbon">
                        <i class="fa-regular fa-star me-1"></i>Meilleur offre</span>
                    <h6 class="text-muted">Pack 100 tickets</h6>
                    <h3 class="price mb-0">
                        <span class="js-price" data-ht="3700">3 700</span>€
                    </h3>
                    <p class="text-secondary" data-suffix="pack">/ pack H.T.</p>
                    <ul class="list-unstyled small">
                        <li class="mb-2 fw-bold">
                            <i class="fa-regular fa-check text-success me-2"></i>100 x 30 minutes
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>
                            <span class="js-per" data-ht="37">37</span>€ / ticket
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>Validité 2 ans
                        </li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100">Souscrire</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 position-relative">
                    <span class="ribbon">
                        <i class="fa-regular fa-bolt me-1"></i>Meilleur prix</span>
                    <h6 class="text-muted">Pack 400 tickets</h6>
                    <h3 class="price mb-0">
                        <span class="js-price" data-ht="13600">13 600</span>€
                    </h3>
                    <p class="text-secondary" data-suffix="pack">/ pack H.T.</p>
                    <ul class="list-unstyled small">
                        <li class="mb-2 fw-bold">
                            <i class="fa-regular fa-check text-success me-2"></i>400 x 30 minutes
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>
                            <span class="js-per" data-ht="34">34</span>€ / ticket
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-check text-success me-2"></i>Validité 3 ans
                        </li>
                    </ul>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark w-100">Souscrire</a>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- TARIFICATION (règles simples) --}}
<section id="tarification" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Tarification</h2>
        <div class="gradient-bar"></div>

        <div
            class="row g-4 mt-1">
            <!-- Col: Unités de 30 minutes -->
            <div class="col-lg-6">
                <div class="p-4 bg-light border rounded-4 shadow-soft h-100">
                    <div class="d-flex flex-column flex-sm-row align-items-start gap-3 mb-2">
                        <div class="feature-icon mx-auto mx-sm-0">
                            <i class="fa-regular fa-stopwatch"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Unités de 30 minutes</h5>
                            <p class="text-secondary opacity-75 mb-2">Les prestations sont articulées par unité de
                                <strong>30 minutes</strong>
                                (voir tarifs). Toute unité commencée est
                                <strong>due</strong>
                                et n’est valable que 30 minutes, hors forfaits et contrats spécifiques.
                            </p>
                            <p class="text-secondary opacity-75 mb-0">Exemples : 45&nbsp;min =
                                <strong>2 tickets</strong>
                                (arrondi au supérieur) ; 2h15 =
                                <strong>5 tickets</strong>. Les tickets s’achètent à l’unité ou par pack et sont consommés par tranches de 30&nbsp;min. Les interventions sont réalisées en priorité à distance ; le sur site est possible selon la zone et le planning. Des majorations peuvent s’appliquer en dehors des horaires standards (nuit, week-end, jour férié) ainsi que pour l’<strong>urgence</strong>
                                ou un
                                <strong>déplacement Paris/RP</strong>
                                — voir grille ci-contre et simulateur ci-dessous.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex flex-column flex-sm-row align-items-start gap-3 mb-2">
                        <div class="feature-icon mx-auto mx-sm-0">
                            <i class="fa-regular fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Majoration selon le contexte</h5>
                            <p class="text-secondary mb-0">En dehors des horaires standards, des
                                <strong>tickets additionnels</strong>
                                s’ajoutent à la durée estimée (base 1 ticket = 30 min).
                            </p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-4 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-orange">+1 ticket</span>
                                </div>
                                <ul class="small mb-0 text-secondary">
                                    <li class="fw-bold">Nuit</li>
                                    <li class="fw-bold">Week-end</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-4 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-orange">+2 tickets</span>
                                </div>
                                <ul class="small mb-0 text-secondary">
                                    <li class="fw-bold">Jour férié</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 border rounded-4 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-orange">+3 tickets</span>
                                </div>
                                <ul class="small mb-0 text-secondary">
                                    <li class="fw-bold">Urgence</li>
                                    <li class="fw-bold">Déplacement Paris/RP</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border mt-3 mb-0" role="alert">
                        <i class="fa-regular fa-circle-plus me-1"></i>
                        <span class="small">Cumulables : ex.
                            <em>Nuit</em>
                            +
                            <em>Jour férié</em>
                            =
                            <strong>+3 tickets</strong>. Le simulateur ci-dessous calcule automatiquement le total.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>