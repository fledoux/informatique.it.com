@extends('layouts.app')

@section('title', 'Modifier la Permission')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fa-regular fa-pen-to-square"></i>
            Modifier la Permission
        </h1>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('permissions.update', $permission) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nom de la permission <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $permission->name) }}"
                        required
                    >
                    <div class="form-text">
                        Utilisez uniquement des lettres minuscules, chiffres, points, tirets et underscores.
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <strong>Attention :</strong> Modifier le nom de cette permission affectera tous les endroits où elle est utilisée dans le code.
                    Assurez-vous de mettre à jour vos vues et contrôleurs en conséquence.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-orange">
                        <i class="fa-solid fa-save"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($permission->roles()->count() > 0 || $permission->users()->count() > 0)
        <div class="mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-users"></i>
                        Utilisation actuelle
                    </h5>
                </div>
                <div class="card-body">
                    @if($permission->roles()->count() > 0)
                        <p><strong>Rôles assignés :</strong></p>
                        <ul>
                            @foreach($permission->roles as $role)
                                <li>{{ ucfirst($role->name) }}</li>
                            @endforeach
                        </ul>
                    @endif
                    
                    @if($permission->users()->count() > 0)
                        <p><strong>Utilisateurs assignés directement :</strong> {{ $permission->users()->count() }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
