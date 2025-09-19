{{-- SIMULATEUR DE TARIFICATION --}}
<section id="simulateur" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Simulateur rapide</h2>
        <div class="gradient-bar"></div>

        <div
            class="row g-4 mt-1">
            <!-- Entrées -->
            <div class="col-lg-5">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-calculator me-2 text-orange"></i>Paramètres
                    </h5>
                    <div class="mb-5">
                        <label for="calcMinutes" class="form-label">Durée estimée de l'intervention :
                            <span class="totalEnHeure"></span>
                        </label>
                        <div class="input-group">
                            <input type="range" class="form-range" id="calcMinutes" min="30" max="480" value="30" step="30">
                        </div>

                        <div id="calcHelp" class="form-text">Base de calcul : 1 ticket pour 30 minutes (arrondi au supérieur).</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input form-check-input-orange" type="checkbox" id="calcNight">
                                <label class="form-check-label" for="calcNight">Nuit
                                    <span class="badge badge-outline-secondary ms-1 js-extra" data-key="night"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input form-check-input-orange" type="checkbox" id="calcWeekend">
                                <label class="form-check-label" for="calcWeekend">Week-end
                                    <span class="badge badge-outline-secondary ms-1 js-extra" data-key="weekend"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input form-check-input-orange" type="checkbox" id="calcHoliday">
                                <label class="form-check-label" for="calcHoliday">Jour férié
                                    <span class="badge badge-outline-secondary ms-1 js-extra" data-key="holiday"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input form-check-input-orange" type="checkbox" id="calcUrgent">
                                <label class="form-check-label" for="calcUrgent">Urgence
                                    <span class="badge badge-outline-secondary ms-1 js-extra" data-key="urgent"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input form-check-input-orange" type="checkbox" id="calcTravel">
                                <label class="form-check-label" for="calcTravel">Déplacement Paris/RP
                                    <span class="badge badge-outline-secondary ms-1 js-extra" data-key="travel"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button type="button" id="calcReset" class="btn btn-outline-orange w-100 w-sm-auto">
                            <i class="fa-regular fa-rotate-left me-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- Résultats -->
            <div class="col-lg-7">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-ticket me-2 text-orange"></i>Résultat pour
                        <span class="totalEnHeure"></span>
                        d'intervention
                    </h5>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <div class="p-2 px-3 border border-2 rounded-4 text-center text-uppercase fw-bold w-100 w-sm-auto">
                            <small class="text-muted">Base :
                                <span class="fw-bold" id="outBase">0</span>
                                <i class="fa-regular fa-ticket ms-1"></i>
                            </small>
                        </div>
                        <div class="p-2 px-3 border border-2 rounded-4 text-center text-uppercase fw-bold text-muted w-100 w-sm-auto">
                            <small>+ Majoration :
                                <span class="fw-bold" id="outExtra">0</span>
                                <i class="fa-regular fa-ticket ms-1"></i>
                            </small>
                        </div>
                        <div class="p-2 px-3 border border-2 border-orange rounded-4 text-center text-uppercase bg-light fw-bold text-orange w-100 w-sm-auto">
                            <small class="">Total :
                                <span class="fw-bold" id="outTotal">0</span>
                                <i class="fa-regular fa-ticket ms-1 text-orange"></i>
                            </small>
                        </div>
                    </div>

                    {{-- Aide au scroll – visible uniquement en petit écran --}}
                    <div class="d-sm-none p-2 mb-3 text-center bg-orange text-white border rounded-4 text-uppercase">
                        <i class="fa-regular fa-arrows-left-right me-1 fa-2xl"></i><br>
                        Faites glisser<br>le tableau horizontalement<br>pour voir la suite
                    </div>

                    {{-- Ajoute la classe scroll-shadow au wrapper --}}
                    <div class="table-responsive scroll-shadow">
                        <table
                            class="table border border-2 border-secondary align-middle mb-0">{{-- ... le contenu de la table reste inchangé ... --}}
                        </table>
                    </div>

                    <div class="table-responsive">
                        <table class="table border border-2 border-secondary align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Offres</th>
                                    <th class="text-end bg-info bg-opacity-25 text-nowrap">Ticket HT</th>
                                    <th class="text-end bg-info bg-opacity-25">Total HT</th>
                                    <th class="text-end bg-info bg-opacity-50 text-nowrap">Ticket TTC</th>
                                    <th class="text-end bg-info bg-opacity-50">Total TTC</th>
                                </tr>
                            </thead>
                            <tbody id="simuRows">
                                <tr data-offre="unit">
                                    <td class="fw-bold">Ticket unitaire</td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="t_ht_unit">68€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="tot_ht_unit">68€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="t_ttc_unit">81,60€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="tot_ttc_unit">81,60€</span>
                                    </td>
                                </tr>
                                <tr data-offre="p10">
                                    <td class="fw-bold">Pack 10</td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="t_ht_p10">57€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="tot_ht_p10">57€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="t_ttc_p10">68,40€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="tot_ttc_p10">68,40€</span>
                                    </td>
                                </tr>
                                <tr data-offre="p50">
                                    <td class="fw-bold">Pack 50</td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="t_ht_p50">46€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="tot_ht_p50">46€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="t_ttc_p50">55,20€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="tot_ttc_p50">55,20€</span>
                                    </td>
                                </tr>
                                <tr data-offre="p100">
                                    <td class="fw-bold">Pack 100</td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="t_ht_p100">37€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-25">
                                        <span id="tot_ht_p100">37€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="t_ttc_p100">44,40€</span>
                                    </td>
                                    <td class="text-end bg-info bg-opacity-50">
                                        <span id="tot_ttc_p100">44,40€</span>
                                    </td>
                                </tr>
                                <tr data-offre="p400">
                                    <td class="fw-bold">Pack 400</td>
                                    <td class="text-end bg-success bg-opacity-25">
                                        <span id="t_ht_p400">34€</span>
                                    </td>
                                    <td class="text-end bg-success bg-opacity-25">
                                        <span id="tot_ht_p400">34€</span>
                                    </td>
                                    <td class="text-end bg-success bg-opacity-50">
                                        <span id="t_ttc_p400">40,80€</span>
                                    </td>
                                    <td class="text-end bg-success bg-opacity-50">
                                        <span id="tot_ttc_p400">40,80€</span>
                                    </td>
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