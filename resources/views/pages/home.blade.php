@extends('layouts.public')

@section('title', __('Home.Welcome'))

@section('content')

@include('pages.hero')

{{-- PROBLEMES / SOLUTIONS --}}
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center">8 raisons de travailler avec nous</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-clock text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Intervention rapide</h4>
                    <p class="text-secondary mb-0">Support sous 24h ouvrées, assistance urgente disponible</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-user-shield text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Expertise certifiée</h4>
                    <p class="text-secondary mb-0">Techniciens qualifiés, certifications Microsoft & Google</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-euro-sign text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Tarifs transparents</h4>
                    <p class="text-secondary mb-0">Pas de surprise, devis clair, facturation précise</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-handshake text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Relation de confiance</h4>
                    <p class="text-secondary mb-0">Partenaire durable pour votre transformation digitale</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-shield-halved text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Sécurité renforcée</h4>
                    <p class="text-secondary mb-0">Cybersécurité, sauvegardes, protection anti-malware</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-globe text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Support à distance</h4>
                    <p class="text-secondary mb-0">Intervention immédiate partout en France</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-tools text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Maintenance proactive</h4>
                    <p class="text-secondary mb-0">Prévention des pannes, mises à jour automatisées</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 text-center text-sm-start">
                    <i class="fa-solid fa-heart text-orange fs-3 mb-3 d-block"></i>
                    <h4 class="fw-bold mb-2">Satisfaction client</h4>
                    <p class="text-secondary mb-0">Note moyenne 4.8/5 sur plus de 370 interventions</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- OFFRES --}}
<section id="offres" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Nos offres</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <i class="fa-solid fa-headset text-orange fs-3 mb-3 d-block text-center"></i>
                    <h3 class="text-center fw-bold mb-3">Support ponctuel</h3>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Dépannage immédiat</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Assistance à distance</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Intervention sur site</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Diagnostic gratuit</li>
                    </ul>
                    <div class="text-center">
                        <a href="#contact" class="btn btn-outline-orange">Demander un devis</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100 position-relative">
                    <div class="badge bg-orange position-absolute top-0 start-50 translate-middle px-3 py-2">
                        <i class="fa-solid fa-star me-1"></i>Populaire
                    </div>
                    <i class="fa-solid fa-cog text-orange fs-3 mb-3 d-block text-center mt-3"></i>
                    <h3 class="text-center fw-bold mb-3">Infogérance</h3>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Maintenance préventive</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Monitoring 24/7</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Sauvegardes automatiques</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Support illimité</li>
                    </ul>
                    <div class="text-center">
                        <a href="#contact" class="btn btn-orange">Découvrir l'offre</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <i class="fa-solid fa-graduation-cap text-orange fs-3 mb-3 d-block text-center"></i>
                    <h3 class="text-center fw-bold mb-3">Formation & Conseil</h3>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Formation utilisateurs</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Audit sécurité</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Conseil stratégique</li>
                        <li class="mb-2"><i class="fa-solid fa-circle-check check me-2"></i>Accompagnement projet</li>
                    </ul>
                    <div class="text-center">
                        <a href="#contact" class="btn btn-outline-orange">En savoir plus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TARIFS --}}
<section id="tarifs" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Tarifs transparents</h2>
        <div class="gradient-bar"></div>

        <div class="row g-4 mt-4 align-items-stretch">
            <div class="col-md-12 col-lg-6">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h4 class="fw-bold mb-1">Ticket unitaire</h4>
                        <p class="mb-0 text-secondary">Dépannage ponctuel, 30 minutes d'intervention</p>
                    </div>
                    <div class="text-center text-md-end">
                        <div class="h3 fw-bold mb-0"><span class="js-price" data-ht="68">68</span>€ <small class="text-muted" data-suffix="ticket">H.T.</small></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="p-4 bg-white bg-opacity-25 border rounded-4 h-100 d-flex flex-column justify-content-center gap-2">
                    <div class="form-check form-switch d-flex align-items-center gap-3">
                        <input class="form-check-input" type="checkbox" id="toggleTTC">
                        <label class="form-check-label fw-bold" for="toggleTTC">
                            Afficher les prix T.T.C. (+20% de TVA)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100">
                    <div class="text-center">
                        <h4 class="fw-bold mb-2">Pack 10 tickets</h4>
                        <div class="h4 fw-bold text-orange mb-2">
                            <span class="js-price" data-ht="570">570</span>€
                        </div>
                        <p class="small text-muted mb-3" data-suffix="pack">H.T.</p>
                        <div class="badge bg-success mb-3">
                            <span class="js-per" data-ht="57">57</span>€ / ticket
                        </div>
                        <ul class="list-unstyled small text-start">
                            <li class="mb-1">✓ Valable 1 an</li>
                            <li class="mb-1">✓ Interventions à distance</li>
                            <li class="mb-1">✓ Support email inclus</li>
                        </ul>
                        <a href="#contact" class="btn btn-outline-dark btn-sm w-100">Commander</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100">
                    <div class="text-center">
                        <h4 class="fw-bold mb-2">Pack 50 tickets</h4>
                        <div class="h4 fw-bold text-orange mb-2">
                            <span class="js-price" data-ht="2300">2300</span>€
                        </div>
                        <p class="small text-muted mb-3" data-suffix="pack">H.T.</p>
                        <div class="badge bg-success mb-3">
                            <span class="js-per" data-ht="46">46</span>€ / ticket
                        </div>
                        <ul class="list-unstyled small text-start">
                            <li class="mb-1">✓ Valable 1 an</li>
                            <li class="mb-1">✓ Priorité support</li>
                            <li class="mb-1">✓ Déplacements inclus</li>
                        </ul>
                        <a href="#contact" class="btn btn-outline-dark btn-sm w-100">Commander</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 position-relative">
                    <div class="badge bg-orange position-absolute top-0 start-50 translate-middle px-3 py-2">
                        <i class="fa-solid fa-star me-1"></i>Populaire
                    </div>
                    <div class="text-center mt-3">
                        <h4 class="fw-bold mb-2">Pack 100 tickets</h4>
                        <div class="h4 fw-bold text-orange mb-2">
                            <span class="js-price" data-ht="3700">3700</span>€
                        </div>
                        <p class="small text-muted mb-3" data-suffix="pack">H.T.</p>
                        <div class="badge bg-success mb-3">
                            <span class="js-per" data-ht="37">37</span>€ / ticket
                        </div>
                        <ul class="list-unstyled small text-start">
                            <li class="mb-1">✓ Valable 1 an</li>
                            <li class="mb-1">✓ Support prioritaire 24/7</li>
                            <li class="mb-1">✓ Infogérance incluse</li>
                        </ul>
                        <a href="#contact" class="btn btn-orange btn-sm w-100">Commander</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-info border border-info bg-opacity-50 rounded-4 shadow-soft h-100 position-relative">
                    <div class="text-center">
                        <h4 class="fw-bold mb-2">Pack 400 tickets</h4>
                        <div class="h4 fw-bold text-orange mb-2">
                            <span class="js-price" data-ht="13600">13600</span>€
                        </div>
                        <p class="small text-muted mb-3" data-suffix="pack">H.T.</p>
                        <div class="badge bg-success mb-3">
                            <span class="js-per" data-ht="34">34</span>€ / ticket
                        </div>
                        <ul class="list-unstyled small text-start">
                            <li class="mb-1">✓ Valable 1 an</li>
                            <li class="mb-1">✓ Technicien dédié</li>
                            <li class="mb-1">✓ SLA garanti</li>
                        </ul>
                        <a href="#contact" class="btn btn-outline-dark btn-sm w-100">Commander</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- SIMULATEUR DE TARIFICATION --}}
<section id="simulateur" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Simulateur rapide</h2>
        <div class="gradient-bar"></div>

        <div class="row g-4 mt-1">
            {{-- Entrées --}}
            <div class="col-lg-5">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-calculator me-2 text-orange"></i>Paramètres</h5>
                    <div class="mb-5">
                        <label for="calcMinutes" class="form-label">Durée estimée de l'intervention :
                            <span class="totalMinutes fw-bold">30</span> minutes
                        </label>
                        <div class="input-group">
                            <input type="range" class="form-range" id="calcMinutes" min="15" max="240" value="30" step="15">
                            <span class="input-group-text">min</span>
                        </div>
                        <div id="calcHelp" class="form-text">Base de calcul : 1 ticket pour 30 minutes (arrondi au supérieur).</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="calcDeplacement">
                                <label class="form-check-label" for="calcDeplacement">Déplacement</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="calcUrgence">
                                <label class="form-check-label" for="calcUrgence">Urgence (+50%)</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="calcExpress">
                                <label class="form-check-label" for="calcExpress">Express (+30%)</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <input type="number" class="form-control" id="calcKm" placeholder="Km" min="0" max="200" disabled>
                        </div>
                        <div class="col-12">
                            <select class="form-select" id="calcPack">
                                <option value="68">Ticket unitaire (68€)</option>
                                <option value="57">Pack 10 (57€/ticket)</option>
                                <option value="46">Pack 50 (46€/ticket)</option>
                                <option value="37" selected>Pack 100 (37€/ticket)</option>
                                <option value="34">Pack 400 (34€/ticket)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button type="button" id="calcReset" class="btn btn-outline-orange w-100 w-sm-auto">
                            <i class="fa-regular fa-arrow-rotate-left me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Résultats --}}
            <div class="col-lg-7">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-ticket me-2 text-orange"></i>Résultat pour
                        <span class="totalEnHeure"></span>
                        d'intervention</h5>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <div class="p-2 px-3 border border-2 rounded-4 text-center text-uppercase fw-bold w-100 w-sm-auto">
                            <i class="fa-regular fa-ticket me-1"></i>
                            <span class="totalTickets">1</span> ticket<span class="plurielTickets">s</span>
                        </div>
                        <div class="p-2 px-3 border border-2 rounded-4 text-center text-uppercase fw-bold text-muted w-100 w-sm-auto">
                            <i class="fa-regular fa-plus me-1"></i>
                            <span class="totalSupp">0</span>€ suppl.
                        </div>
                        <div class="p-2 px-3 border border-2 border-orange rounded-4 text-center text-uppercase bg-light fw-bold text-orange w-100 w-sm-auto">
                            <i class="fa-regular fa-euro-sign me-1"></i>
                            <span class="totalHT">37</span>€ H.T.
                        </div>
                    </div>

                    {{-- Aide au scroll – visible uniquement en petit écran --}}
                    <div class="d-sm-none p-2 mb-3 text-center bg-orange text-white border rounded-4 text-uppercase">
                        <i class="fa-regular fa-arrows-left-right me-1 fa-2xl"></i><br>
                        Faites glisser<br>le tableau horizontalement<br>pour voir la suite
                    </div>

                    {{-- Ajoute la classe scroll-shadow au wrapper --}}
                    <div class="table-responsive scroll-shadow">
                        <table class="table border border-2 border-secondary align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Pack</th>
                                    <th>Prix/ticket</th>
                                    <th>Tickets</th>
                                    <th>Base</th>
                                    <th>Suppl.</th>
                                    <th>H.T.</th>
                                    <th>T.T.C.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-light">
                                    <td><small>Unitaire</small></td>
                                    <td>68€</td>
                                    <td class="totalTickets">1</td>
                                    <td class="calcBaseUnitaire">68€</td>
                                    <td class="totalSupp">0€</td>
                                    <td class="fw-bold calcTotalHTUnitaire">68€</td>
                                    <td class="calcTotalTTCUnitaire">82€</td>
                                </tr>
                                <tr class="table-light">
                                    <td><small>Pack 10</small></td>
                                    <td>57€</td>
                                    <td class="totalTickets">1</td>
                                    <td class="calcBasePack10">57€</td>
                                    <td class="totalSupp">0€</td>
                                    <td class="fw-bold calcTotalHTPack10">57€</td>
                                    <td class="calcTotalTTCPack10">68€</td>
                                </tr>
                                <tr class="table-light">
                                    <td><small>Pack 50</small></td>
                                    <td>46€</td>
                                    <td class="totalTickets">1</td>
                                    <td class="calcBasePack50">46€</td>
                                    <td class="totalSupp">0€</td>
                                    <td class="fw-bold calcTotalHTPack50">46€</td>
                                    <td class="calcTotalTTCPack50">55€</td>
                                </tr>
                                <tr class="table-success fw-bold">
                                    <td><small>Pack 100</small> <i class="fa-regular fa-star text-warning"></i></td>
                                    <td>37€</td>
                                    <td class="totalTickets">1</td>
                                    <td class="calcBasePack100">37€</td>
                                    <td class="totalSupp">0€</td>
                                    <td class="calcTotalHTPack100">37€</td>
                                    <td class="calcTotalTTCPack100">44€</td>
                                </tr>
                                <tr class="table-light">
                                    <td><small>Pack 400</small></td>
                                    <td>34€</td>
                                    <td class="totalTickets">1</td>
                                    <td class="calcBasePack400">34€</td>
                                    <td class="totalSupp">0€</td>
                                    <td class="fw-bold calcTotalHTPack400">34€</td>
                                    <td class="calcTotalTTCPack400">41€</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 small text-muted">Les montants ci-dessus sont calculés selon vos paramètres (HT et TTC affichés).</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TeamViewer --}}
<section class="py-5">
    <div class="container">
        <div class="hero rounded-4 p-4">
            <div class="row mt-0">
                <div class="col-md-8 mx-auto">
                    <div class="d-flex flex-column flex-sm-row align-items-center gap-3 mb-2">
                        <div>
                            <img src="{{ asset('assets/img/logo/tv-logo.svg') }}" alt="TeamViewer" style="height:30px;">
                        </div>
                        <div class="text-center text-sm-start">
                            <h5 class="fw-bold mb-1">Assistance immédiate</h5>
                            <p class="text-secondary mb-0">Prise en main à distance sécurisée</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CAS CLIENTS --}}
<section id="cas-clients" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Cas clients</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-building-circle-check text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Cabinet d'avocats — 25 postes</h5>
                    </div>
                    <p class="text-secondary">Migrations M365, MFA, chiffrement disques, sauvegardes cloud, charte sécurité.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>98,2%</strong> de disponibilité (SLA respecté)
                        </li>
                        <li>Audit sécurité → plan d'action en 30 jours</li>
                        <li>SLA respecté à <strong>99,8%</strong></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-store text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Commerce multi-sites — 12 caisses</h5>
                    </div>
                    <p class="text-secondary">Wi-Fi unifié, VLAN invités, caisse connectée, supervision, sauvegardes locales + cloud.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>Zéro panne</strong> système de caisse en 18 mois
                        </li>
                        <li>Support 7j/7 en heures clés</li>
                        <li>Tableau de bord mensuel</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-house-signal text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Particulier — Maison connectée</h5>
                    </div>
                    <p class="text-secondary">Sécurisation Wi-Fi, sauvegarde photos/Docs, tri cloud, formation usage quotidien.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>Virus éliminés</strong>, Temps de démarrage PC ÷2
                        </li>
                        <li>Photos sécurisées et triées</li>
                        <li>Assistance à distance illimitée</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-building-shield text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Start-up SaaS — 40 utilisateurs</h5>
                    </div>
                    <p class="text-secondary">Onboarding/Offboarding automatisés, SSO, MFA obligatoire, durcissement postes, sauvegardes cloud.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>15 min</strong> pour créer un nouvel utilisateur complet
                        </li>
                        <li>
                            <strong>Zero incident</strong> sécurité depuis la mise en place
                        </li>
                        <li>Conformité renforcée (SSO + MFA)</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-truck-fast text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Logistique — 3 entrepôts</h5>
                    </div>
                    <p class="text-secondary">Wi-Fi industriel, redondance liens, supervision 24/7, procédures incident, support multisites.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>99,7%</strong> uptime Wi-Fi (vs 87% avant)
                        </li>
                        <li>Dispo Wi-Fi picking : <strong>99,9%</strong></li>
                        <li>MTTR divisé par 2</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-regular fa-city text-orange fs-3 me-2"></i>
                        <h5 class="mb-0">Collectivité — 120 postes</h5>
                    </div>
                    <p class="text-secondary">Inventaire centralisé, gestion correctives/préventives, sauvegardes 3-2-1, plan PRA, sensibilisation.</p>
                    <ul class="small text-secondary mb-0">
                        <li>
                            <strong>-47%</strong> d'incidents récurrents en 1 an
                        </li>
                        <li>Sauvegardes vérifiées mensuellement</li>
                        <li>Conformité RGPD renforcée</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- AVIS --}}
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Ce que disent nos&nbsp;clients</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/abhaye_g.jpg') }}" alt="Abhaye G." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Abhaye G.</h6>
                            <small class="text-muted">CEO, Tech Startup</small>
                        </div>
                    </div>
                    <p class="mb-0">"Exceptionally responsive and truly supportive — we now benefit from clear procedures and rock-solid backups."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/man.jpg') }}" alt="Marc D." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Marc D.</h6>
                            <small class="text-muted">Gérant, Commerce</small>
                        </div>
                    </div>
                    <p class="mb-0">"Notre réseau ne tombe plus. Le support comprend nos heures de pointe. Zéro stress."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/emilie_p.jpg') }}" alt="Emilie P." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Emilie P.</h6>
                            <small class="text-muted">Architecte</small>
                        </div>
                    </div>
                    <p class="mb-0">"Télémaintenance + petites formations : j'utilise mieux mon Mac et mes données sont en sécurité."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/sabine_l.jpg') }}" alt="Sabine L." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Sabine L.</h6>
                            <small class="text-muted">Responsable IT</small>
                        </div>
                    </div>
                    <p class="mb-0">"Runbook clair, astreinte efficace, et surtout un vrai suivi des correctifs. On dort mieux."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/francois_g.jpg') }}" alt="François G." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">François G.</h6>
                            <small class="text-muted">RH</small>
                        </div>
                    </div>
                    <p class="mb-0">"Onboarding des nouveaux en 15 min, accès prêts, checklist au cordeau. Gain de temps énorme."</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/man.jpg') }}" alt="Jérôme K." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Jérôme K.</h6>
                            <small class="text-muted">Avocat</small>
                        </div>
                    </div>
                    <p class="mb-0">"Support réactif et pédago. On a retrouvé de la fluidité au quotidien dans un domaine où nous devons être très réactifs."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/elie_e.jpg') }}" alt="Elie E." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Elie E.</h6>
                            <small class="text-muted">Dirigeant</small>
                        </div>
                    </div>
                    <p class="mb-0">"Migration M365 sans coupure et sécurisation MFA en 48h. Équipe carrée et dispo."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/remi_f.jpg') }}" alt="Rémi F." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Rémi F.</h6>
                            <small class="text-muted">Logistique</small>
                        </div>
                    </div>
                    <p class="mb-0">"Ils ont trouvé la panne réseau qui bloquait nos commandes. Depuis, tout roule."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex align-items-center mb-2">
                        <img class="rounded-circle me-3" src="{{ asset('assets/img/customer_case/man.jpg') }}" alt="Laurent P." style="width: 48px; height: 48px;">
                        <div>
                            <h6 class="mb-0 fw-bold">Laurent P.</h6>
                            <small class="text-muted">Particulier</small>
                        </div>
                    </div>
                    <p class="mb-0">"Intervention à distance en 30 min, sauvegardes mises en place : on est sereins."</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">FAQ</h2>
        <div class="gradient-bar"></div>
        <div class="row justify-content-center mt-3">
            <div class="col-lg-10">
                <div class="accordion" id="faqAcc">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
                                Combien coûte une intervention de dépannage ?
                            </button>
                        </h2>
                        <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAcc">
                            <div class="accordion-body">
                                <p>Nos tarifs sont transparents : <strong>68€ HT</strong> pour un ticket unitaire (30 minutes). 
                                Nous proposons des packs avantageux : 10 tickets à 57€/ticket, 50 tickets à 46€/ticket, 
                                100 tickets à 37€/ticket, et 400 tickets à 34€/ticket.</p>
                                <p>Des suppléments peuvent s'appliquer : déplacement (0,60€/km), urgence (+50%) et express (+30%).</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
                                Quels sont vos délais d'intervention ?
                            </button>
                        </h2>
                        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                            <div class="accordion-body">
                                <p>Support standard sous <strong>24h ouvrées</strong>. Pour les urgences, nous intervenons 
                                sous <strong>2h</strong> avec le supplément express (+30%).</p>
                                <p>Nos horaires : 9h-18h du lundi au vendredi. Astreinte disponible pour nos clients sous contrat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
                                Intervenez-vous à distance et sur site ?
                            </button>
                        </h2>
                        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                            <div class="accordion-body">
                                <p>Oui, nous privilégions <strong>l'assistance à distance</strong> (TeamViewer, RDP) 
                                pour une résolution rapide et économique.</p>
                                <p>Nous nous déplaçons également <strong>sur site</strong> quand c'est nécessaire 
                                (installation matériel, problèmes réseau). Zone d'intervention : Île-de-France et remote partout en France.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">
                                Que comprend votre offre d'infogérance ?
                            </button>
                        </h2>
                        <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                            <div class="accordion-body">
                                <p>Notre service d'infogérance inclut : monitoring 24/7, maintenance préventive, 
                                sauvegardes automatiques, gestion des mises à jour, support illimité, 
                                et tableau de bord mensuel.</p>
                                <p>Nous devenons votre <strong>DSI externalisé</strong> avec un engagement de niveau de service (SLA).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CONTACT --}}
<section id="contact" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Parlons de vos&nbsp;besoins</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-lg-7">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    {{-- Contact form will be implemented later with contact functionality --}}
                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="contact_name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="contact_email" name="email" required>
                            </div>
                            <div class="col-12">
                                <label for="contact_company" class="form-label">Entreprise</label>
                                <input type="text" class="form-control" id="contact_company" name="company">
                            </div>
                            <div class="col-12">
                                <label for="contact_subject" class="form-label">Sujet *</label>
                                <select class="form-select" id="contact_subject" name="subject" required>
                                    <option value="">Choisir...</option>
                                    <option value="devis">Demande de devis</option>
                                    <option value="info">Informations générales</option>
                                    <option value="urgence">Urgence technique</option>
                                    <option value="partenariat">Partenariat</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contact_message" class="form-label">Message *</label>
                                <textarea class="form-control" id="contact_message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-orange">
                                    <i class="fa-regular fa-paper-plane me-1"></i>
                                    Envoyer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-clock me-2 text-orange"></i>Horaires & zones</h5>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <strong>Lun-Ven :</strong> 9h-18h
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-phone me-2"></i>
                            <strong>Tél :</strong> 01 49 66 21 77
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-envelope me-2"></i>
                            <strong>Email :</strong> contact@informatique.it.com
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-map-marker-alt me-2"></i>
                            <strong>Zone :</strong> Île-de-France + remote France
                        </li>
                    </ul>
                    <hr class="my-4">
                    <h6 class="fw-bold">Besoin urgent ?</h6>
                    <p class="mb-2">Activez un ticket prioritaire :</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-orange w-100 w-sm-auto">
                        <i class="fa-regular fa-bolt me-1"></i>
                        Support express
                    </a>
                    <hr class="my-4">
                    <img src="{{ asset('assets/img/logo/tv-logo.svg') }}" alt="TeamViewer" class="img-fluid logo-tv" style="height: 24px;">
                    <p class="mt-4">
                        <strong>Assistance immédiate :</strong><br>
                        Prise en main à distance sécurisée pour un diagnostic et une résolution rapides.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="py-5 text-center">
    <div class="container">
        <div class="hero rounded-4 p-4">
            <h2 class="fw-bold mb-2">Fini les galères Informatiques.</h2>
            <p class="text-secondary mb-4">Nous dépannons aujourd'hui. Anticipons vos besoins de demain. Sécurisons votre avenir.</p>
            <a href="#contact" class="btn btn-orange btn-lg px-5">
                <i class="fa-regular fa-handshake me-2"></i>
                Ensemble, sécurisons vos ambitions
            </a>
        </div>
    </div>
</section>

{{-- TARIFICATION (règles simples) --}}
<section id="tarification" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center">Tarification</h2>
        <div class="gradient-bar"></div>

        <div class="row g-4 mt-1">
            {{-- Col: Unités de 30 minutes --}}
            <div class="col-lg-6">
                <div class="p-4 bg-light border rounded-4 shadow-soft h-100">
                    <div class="d-flex flex-column flex-sm-row align-items-start gap-3 mb-2">
                        <div class="feature-icon mx-auto mx-sm-0">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Unités de 30 minutes</h5>
                            <p class="text-secondary mb-3">1 ticket = 30 minutes d'intervention<br>
                                Temps arrondi au ticket supérieur</p>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fa-regular fa-check text-success me-2"></i>
                                    15 min → 1 ticket
                                </li>
                                <li class="mb-2">
                                    <i class="fa-regular fa-check text-success me-2"></i>
                                    45 min → 2 tickets
                                </li>
                                <li class="mb-2">
                                    <i class="fa-regular fa-check text-success me-2"></i>
                                    1h20 → 3 tickets
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <div class="d-flex flex-column flex-sm-row align-items-start gap-3 mb-2">
                        <div class="feature-icon mx-auto mx-sm-0">
                            <i class="fa-regular fa-layer-plus"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Suppléments facturables</h5>
                            <p class="text-secondary mb-3">Coûts additionnels selon contexte</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-2 bg-light rounded text-center">
                                <i class="fa-regular fa-car text-orange d-block mb-1"></i>
                                <strong>Déplacement</strong><br>
                                <small>0.60€/km</small><br>
                                <small class="text-muted">A/R depuis Boulogne</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2 bg-light rounded text-center">
                                <i class="fa-regular fa-moon text-orange d-block mb-1"></i>
                                <strong>Urgence</strong><br>
                                <small>+50%</small><br>
                                <small class="text-muted">Soir/W.end/Fériés</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-2 bg-light rounded text-center">
                                <i class="fa-regular fa-stopwatch text-orange d-block mb-1"></i>
                                <strong>Express</strong><br>
                                <small>+30%</small><br>
                                <small class="text-muted">< 2h ouvrées</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border mt-3 mb-0" role="alert">
                        <i class="fa-regular fa-circle-plus me-1"></i>
                        <span class="small">Cumulables : ex. 
                            <strong>Intervention urgente</strong> de 45 min le samedi avec déplacement 20km = 
                            <strong>2 tickets × 1.5 + 24€</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Include the home script --}}
@push('javascripts')
    @include('home.script-home')
@endpush

@endsection