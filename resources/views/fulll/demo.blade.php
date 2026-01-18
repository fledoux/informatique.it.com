@extends('layouts.app')

@section('title', 'Intégration Full.io - Demo')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-10">
            <h1 class="mb-4">🔗 Intégration Full.io</h1>

            <!-- État de la configuration -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <h5 class="mb-0">📋 État actuel</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Base URL:</strong></p>
                            <code>{{ config('services.fulll.base_url') }}</code>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Client ID:</strong></p>
                            <code>{{ config('services.fulll.client_id') ? substr(config('services.fulll.client_id'), 0, 8) . '...' : '❌ Non configuré' }}</code>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Access Token:</strong></p>
                            <code>{{ config('services.fulll.access_token') ? substr(config('services.fulll.access_token'), 0, 20) . '...' : '❌ Non configuré' }}</code>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Refresh Token:</strong></p>
                            <code>{{ config('services.fulll.refresh_token') ? '✅ Configuré' : '❌ Non configuré' }}</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">⚙️ Configuration requise</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>
                            <p><strong>Générer les credentials OAuth 2.0 du cabinet</strong></p>
                            <p>Demandez à l'administrateur du cabinet full.io de :</p>
                            <ul>
                                <li>Aller sur <code>https://admin.fulll.io/settings/security/api-key</code></li>
                                <li>Cliquer sur "Générer les informations d'identification"</li>
                                <li>Copier les 4 valeurs affichées</li>
                            </ul>
                        </li>
                        <li>
                            <p><strong>Ajouter dans le fichier <code>.env</code></strong></p>
                            <pre><code>FULLL_CLIENT_ID=&lt;id-client&gt;
FULLL_CLIENT_SECRET=&lt;secret-client&gt;
FULLL_ACCESS_TOKEN=&lt;jeton-d-acces&gt;
FULLL_REFRESH_TOKEN=&lt;jeton-actualisation&gt;
FULLL_BASE_URL=https://api.fulll.io</code></pre>
                        </li>
                        <li>
                            <p><strong>Tester la connexion</strong></p>
                            <pre><code>php artisan config:clear
php artisan fulll:test-connection</code></pre>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Exemples d'API -->
            <div class="card">
                <div class="card-header bg-success">
                    <h5 class="mb-0">📡 Exemples d'API</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="apiExamples">
                        @foreach($examples as $index => $example)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#example{{ $index }}">
                                    {{ $example['title'] }}
                                    <code class="ms-2">{{ $example['endpoint'] }}</code>
                                </button>
                            </h2>
                            <div id="example{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#apiExamples">
                                <div class="accordion-body">
                                    <p><strong>Endpoint:</strong></p>
                                    <code>{{ config('services.fulll.base_url') . str_replace('{id}', '123', $example['endpoint']) }}</code>

                                    @if($example['payload'])
                                    <p class="mt-3"><strong>Payload (JSON):</strong></p>
                                    <pre><code>{{ json_encode($example['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>

                                    <p><strong>cURL:</strong></p>
                                    <pre><code>curl -X {{ explode(' ', $example['endpoint'])[0] }} {{ config('services.fulll.base_url') . str_replace('{id}', '123', $example['endpoint']) }} \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{{ json_encode($example['payload'], JSON_UNESCAPED_SLASHES) }}'</code></pre>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Test personnalisé -->
            <div class="card mt-4">
                <div class="card-header bg-secondary">
                    <h5 class="mb-0">🧪 Test avec token personnalisé</h5>
                </div>
                <div class="card-body">
                    <form id="tokenTestForm" class="needs-validation">
                        <div class="mb-3">
                            <label for="testToken" class="form-label">Entrez un token d'accès pour tester :</label>
                            <textarea class="form-control" id="testToken" rows="3" placeholder="Bearer token..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Tester la connexion</button>
                    </form>
                    <div id="testResult" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-2">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">📚 Ressources</h6>
                </div>
                <div class="card-body p-2">
                    <ul class="list-unstyled small">
                        <li><a href="https://admin.fulll.io/settings/security/api-key" target="_blank" class="text-decoration-none">🔑 Générer les credentials</a></li>
                        <li><a href="https://developers.fulll.io/docs/authentication" target="_blank" class="text-decoration-none">📖 Auth Documentation</a></li>
                        <li><a href="https://developers.fulll.io/docs/accounting" target="_blank" class="text-decoration-none">📊 Accounting API</a></li>
                        <li><a href="{{ route('home') }}" class="text-decoration-none">← Retour</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tokenTestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const token = document.getElementById('testToken').value.trim();
    
    if (!token) {
        alert('Veuillez entrer un token');
        return;
    }

    const resultDiv = document.getElementById('testResult');
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Chargement...</span></div>';

    try {
        const response = await fetch('/fulll/demo/test-token/' + encodeURIComponent(token));
        const data = await response.json();

        if (data.status === 200) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>✅ Connexion réussie!</strong>
                    <pre><code>${JSON.stringify(data.response, null, 2)}</code></pre>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ Erreur ${data.status}</strong>
                    <pre><code>${JSON.stringify(data.response, null, 2)}</code></pre>
                </div>
            `;
        }
    } catch (error) {
        resultDiv.innerHTML = `<div class="alert alert-danger"><strong>❌ Erreur:</strong> ${error.message}</div>`;
    }
});
</script>
@endpush
@endsection
