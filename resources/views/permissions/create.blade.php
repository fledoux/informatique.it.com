@extends('layouts.app')

@section('title', 'Créer une Permission Custom')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            
            Créer une Permission Custom
        </h1>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>
    </div>

    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <strong>Important :</strong> Les permissions custom doivent être utilisées avec <code>@@can('nom.permission')</code> 
        dans vos vues ou <code>$this->middleware('permission:nom.permission')</code> dans vos contrôleurs.
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nom de la permission <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="ex: custom.access_reports, custom.export_data"
                        required
                    >
                    <div class="form-text">
                        Format recommandé : <code>préfixe.action</code> (ex: <code>custom.view_analytics</code>).<br>
                        Utilisez uniquement des lettres minuscules, chiffres, points, tirets et underscores.
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <strong>Exemples de permissions custom :</strong>
                    <ul class="mb-0">
                        <li><code>custom.access_reports</code> - Accès aux rapports</li>
                        <li><code>custom.export_data</code> - Export de données</li>
                        <li><code>custom.view_analytics</code> - Voir les analytics</li>
                        <li><code>custom.manage_billing</code> - Gérer la facturation</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-orange">
                        <i class="fa-solid fa-save"></i>
                        Créer la permission
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa-solid fa-lightbulb"></i>
                    Bonnes pratiques
                </h5>
            </div>
            <div class="card-body">
                <ul>
                    <li><strong>Préfixe "custom."</strong> : Utilisez toujours un préfixe pour distinguer vos permissions des permissions système.</li>
                    <li><strong>Noms explicites</strong> : Choisissez des noms qui décrivent clairement l'action (ex: <code>custom.export_invoices</code>).</li>
                    <li><strong>Cohérence</strong> : Utilisez une convention de nommage cohérente dans toute votre application.</li>
                    <li><strong>Documentation</strong> : Documentez l'usage de chaque permission custom dans votre code.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
