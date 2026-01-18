@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">
                <i class="fa-solid fa-plug"></i> Test API full.io
            </h1>

            <!-- Info développement -->
            @if(app()->isLocal())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-flask"></i>
                    <strong>Mode Développement</strong> - Cette page est accessible sans authentification
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Aide pour les credentials -->
            <div class="alert alert-danger" id="credentials-help" style="display: none;">
                <h5><i class="fa-solid fa-triangle-exclamation"></i> Erreur d'authentification</h5>
                <p class="mb-2"><strong>Les endpoints OAuth standard retournent 404.</strong></p>
                <p class="mb-3">Vérifiez les points suivants :</p>
                <ol class="small">
                    <li>✓ Les credentials sont-ils corrects ? Vérifiez dans <strong>app.fulll.io → Paramètres → Intégrations</strong></li>
                    <li>✓ L'intégration est-elle <strong>activée</strong> et <strong>approuvée</strong> ?</li>
                    <li>✓ Vérifiez le <strong>statut</strong> de l'intégration dans full.io</li>
                    <li>✓ Contactez le support full.io si nécessaire</li>
                </ol>
                <p class="mb-0 mt-3">
                    <small class="text-muted">Affichage automatique en cas d'erreur d'authentification 404</small>
                </p>
            </div>

            <!-- Status de connexion -->
            <div class="alert alert-info" id="connection-status" style="display: none;">
                <i class="fa-solid fa-check-circle"></i>
                <span id="status-message"></span>
            </div>

            <!-- Boutons d'action -->
            <div class="mb-4">
                <button class="btn btn-primary" id="test-connection-btn" onclick="testConnection()">
                    <i class="fa-solid fa-wifi"></i> Tester la connexion
                </button>
                <button class="btn btn-info" id="list-clients-btn" onclick="listClients()" disabled>
                    <i class="fa-solid fa-list"></i> Lister les clients
                </button>
            </div>

            <!-- Formulaire de création de client -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-plus-circle"></i> Créer un client
                    </h5>
                </div>
                <div class="card-body">
                    <form id="create-client-form" onsubmit="createClient(event)">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    Nom <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="0123456789">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="siret" class="form-label">SIRET</label>
                                <input type="text" id="siret" name="siret" class="form-control" placeholder="14 chiffres">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" id="address" name="address" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="zip" class="form-label">Code Postal</label>
                                <input type="text" id="zip" name="zip" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" id="city" name="city" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label">Pays (code ISO-2)</label>
                            <input type="text" id="country" name="country" class="form-control" placeholder="FR" maxlength="2">
                        </div>

                        <button type="submit" class="btn btn-success" id="submit-btn" disabled>
                            <i class="fa-solid fa-save"></i> Créer le client
                        </button>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-terminal"></i> Résultats
                    </h5>
                </div>
                <div class="card-body">
                    <pre id="results-box" style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;">
// Les résultats s'afficheront ici...
                    </pre>
                </div>
            </div>
        </div>

        <!-- Sidebar d'aide -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-circle-info"></i> Informations
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="mt-0">API full.io</h6>
                    <p class="small text-muted">
                        Cette page teste la connexion à l'API de comptabilité full.io via OAuth 2.0.
                    </p>

                    <hr>

                    <h6>Étapes</h6>
                    <ol class="small">
                        <li>Cliquez sur "Tester la connexion"</li>
                        <li>Remplissez le formulaire de client</li>
                        <li>Cliquez sur "Créer le client"</li>
                        <li>Vérifiez le résultat dans full.io</li>
                    </ol>

                    <hr>

                    <h6>Configuration requise</h6>
                    <p class="small text-muted">
                        Les variables d'environnement doivent être configurées dans <code>.env</code> :
                    </p>
                    <pre class="small bg-light p-2" style="border-radius: 3px;">FULLL_CLIENT_ID=...
FULLL_CLIENT_SECRET=...</pre>

                    <hr>

                    <h6>Réponses API</h6>
                    <p class="small text-muted">
                        Les réponses JSON s'affichent ci-dessous. Vérifiez les codes HTTP pour diagnostiquer les erreurs.
                    </p>

                    <hr>

                    <a href="https://fulll.stoplight.io/" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="fa-solid fa-external-link"></i> Docs API
                    </a>
                    <a href="https://app.fulll.io/" target="_blank" class="btn btn-sm btn-outline-info w-100">
                        <i class="fa-solid fa-external-link"></i> App full.io
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function testConnection() {
        const btn = document.getElementById('test-connection-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Connexion en cours...';

        fetch('/fulll/test/connection', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data);

            if (data.success) {
                document.getElementById('status-message').textContent = '✓ Connexion réussie à l\'API';
                document.getElementById('connection-status').className = 'alert alert-success';
                document.getElementById('connection-status').style.display = 'block';
                document.getElementById('credentials-help').style.display = 'none';
                document.getElementById('list-clients-btn').disabled = false;
                document.getElementById('submit-btn').disabled = false;
            } else {
                document.getElementById('status-message').textContent = '✗ Erreur de connexion';
                document.getElementById('connection-status').className = 'alert alert-danger';
                document.getElementById('connection-status').style.display = 'block';
                
                // Afficher l'aide si erreur 404 (probablement un problème de credentials)
                if (data.message && data.message.includes('404')) {
                    document.getElementById('credentials-help').style.display = 'block';
                }
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wifi"></i> Tester la connexion';
        })
        .catch(error => {
            displayResults({ success: false, error: error.message });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wifi"></i> Tester la connexion';
        });
    }

    function listClients() {
        const btn = document.getElementById('list-clients-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Chargement...';

        fetch('/fulll/test/clients?limit=10&offset=0', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-list"></i> Lister les clients';
        })
        .catch(error => {
            displayResults({ success: false, error: error.message });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-list"></i> Lister les clients';
        });
    }

    function createClient(event) {
        event.preventDefault();

        const form = document.getElementById('create-client-form');
        const formData = new FormData(form);

        const btn = event.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Création en cours...';

        fetch('/fulll/test/clients', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data);

            if (data.success) {
                alert('Client créé avec succès!');
                form.reset();
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Créer le client';
        })
        .catch(error => {
            displayResults({ success: false, error: error.message });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Créer le client';
        });
    }

    function displayResults(data) {
        const resultsBox = document.getElementById('results-box');
        resultsBox.textContent = JSON.stringify(data, null, 2);
    }

    // Charger la connexion au chargement de la page
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Page chargée. Testez la connexion pour commencer.');
    });
</script>

<style>
    pre {
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }

    .sticky-top {
        z-index: 10;
    }

    .btn-loading {
        pointer-events: none;
    }

    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }

    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection
