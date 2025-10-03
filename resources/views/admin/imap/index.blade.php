@extends('layouts.app')

@section('title', 'Gestion IMAP')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">📧 Gestion IMAP</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configuration IMAP</h5>
                </div>
                <div class="card-body">
                    @if(empty($config['host']) || empty($config['username']))
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">⚠️ Configuration incomplète</h6>
                            <p class="mb-2">Ajoutez les variables suivantes dans votre fichier <code>.env</code> :</p>
                            <pre class="mb-0"><code>IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_USERNAME=your-email@gmail.com
IMAP_PASSWORD=your-app-password
IMAP_ENCRYPTION=ssl
IMAP_FOLDER=INBOX</code></pre>
                        </div>
                    @else
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Serveur</strong></td>
                                    <td>{{ $config['host'] }}:{{ $config['port'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Utilisateur</strong></td>
                                    <td>{{ $config['username'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Chiffrement</strong></td>
                                    <td>{{ strtoupper($config['encryption']) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dossier</strong></td>
                                    <td>{{ $config['folder'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Archivage</strong></td>
                                    <td>
                                        {{ config('imap.move_to_archive') ? 'Activé' : 'Désactivé' }}
                                        @if(config('imap.move_to_archive'))
                                            <br><small class="text-muted">{{ config('imap.archive_folder_pattern') }}</small>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-block">
                        <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                            🔍 Tester la connexion
                        </button>
                        <button type="button" class="btn btn-primary" onclick="fetchEmails()">
                            📥 Récupérer les emails
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Résultats</h5>
                </div>
                <div class="card-body">
                    <div id="results-container">
                        <p class="text-muted mb-0">Aucun test effectué</p>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aide</h5>
                </div>
                <div class="card-body">
                    <h6>Configuration Gmail</h6>
                    <p class="small">
                        Pour Gmail, activez l'authentification à 2 facteurs et générez un 
                        <strong>mot de passe d'application</strong> au lieu d'utiliser votre mot de passe principal.
                    </p>
                    
                    <h6 class="mt-3">Commande Artisan</h6>
                    <p class="small mb-2">Vous pouvez aussi utiliser la commande :</p>
                    <code class="small">php artisan email:fetch</code>
                    
                    <h6 class="mt-3">Automatisation</h6>
                    <p class="small">Ajoutez dans votre crontab :</p>
                    <code class="small">*/5 * * * * php artisan email:fetch</code>
                    
                    <h6 class="mt-3">📁 Archivage</h6>
                    <p class="small">
                        Les emails traités sont automatiquement <strong>marqués comme lus</strong> 
                        et <strong>déplacés</strong> vers <code>INBOX.Archives.{{ date('Y') }}</code>. 
                        Le dossier de l'année est créé automatiquement si nécessaire.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
async function testConnection() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '⏳ Test en cours...';
    
    try {
        const response = await fetch('{{ route("admin.imap.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResults('success', data.message, data.details);
        } else {
            showResults('error', data.message);
        }
    } catch (error) {
        showResults('error', 'Erreur de communication avec le serveur');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

async function fetchEmails() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '⏳ Récupération en cours...';
    
    try {
        const response = await fetch('{{ route("admin.imap.fetch-emails") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showResults('success', data.message);
        } else {
            showResults('error', data.message);
        }
    } catch (error) {
        showResults('error', 'Erreur de communication avec le serveur');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function showResults(type, message, details = null) {
    const container = document.getElementById('results-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? '✅' : '❌';
    
    let html = `
        <div class="alert ${alertClass}">
            <strong>${icon} ${message}</strong>
        </div>
    `;
    
    if (details) {
        html += `
            <table class="table table-sm mt-2">
                <tbody>
        `;
        
        Object.entries(details).forEach(([key, value]) => {
            const label = key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += `<tr><td><strong>${label}</strong></td><td>${value}</td></tr>`;
        });
        
        html += `
                </tbody>
            </table>
        `;
    }
    
    container.innerHTML = html;
}
</script>
@endpush