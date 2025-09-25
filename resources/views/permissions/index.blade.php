@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Permissions</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>
            Retour au Dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fa-solid fa-shield-halved me-2"></i>
                Matrice des Permissions par Rôle
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('permissions.update') }}">
                @csrf
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start" style="min-width: 200px;">Permission</th>
                                @foreach ($roles as $role)
                                    <th class="text-center" style="width: 120px;">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold">{{ ucfirst($role->name) }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentGroup = '';
                            @endphp
                            
                            @foreach ($permissions as $permission)
                                @php
                                    $permissionGroup = explode('.', $permission->name)[0];
                                @endphp
                                
                                @if ($currentGroup !== $permissionGroup)
                                    @if ($currentGroup !== '')
                                        <tr class="table-light">
                                            <td colspan="{{ count($roles) + 1 }}" class="py-1"></td>
                                        </tr>
                                    @endif
                                    <tr class="table-secondary">
                                        <td colspan="{{ count($roles) + 1 }}" class="fw-bold text-uppercase py-2">
                                            <i class="fa-solid fa-folder me-2"></i>
                                            {{ ucfirst($permissionGroup) }}
                                        </td>
                                    </tr>
                                    @php $currentGroup = $permissionGroup; @endphp
                                @endif
                                
                                <tr>
                                    <td class="fw-medium">
                                        <span class="text-muted me-2">{{ explode('.', $permission->name)[0] }}.</span>{{ explode('.', $permission->name)[1] }}
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input type="checkbox" 
                                                       class="form-check-input permission-checkbox" 
                                                       name="permissions[{{ $permission->name }}_{{ $role->name }}]" 
                                                       value="1"
                                                       data-role="{{ $role->name }}"
                                                       data-permission="{{ $permission->name }}"
                                                       {{ $matrix[$permission->name][$role->name] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        <small>
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Cliquez sur les cases en en-tête de colonne pour tout cocher/décocher un rôle
                        </small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                            <i class="fa-solid fa-rotate me-2"></i>
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-save me-2"></i>
                            Enregistrer les Modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Légende des Rôles</h6>
                </div>
                <div class="card-body">
                    @foreach ($roles as $role)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">{{ ucfirst($role->name) }}</span>
                            <small class="text-muted">
                                {{ $role->permissions->count() }} permission(s) assignée(s)
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $roles->count() }}</h4>
                            <small class="text-muted">Rôles</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $permissions->count() }}</h4>
                            <small class="text-muted">Permissions</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">{{ collect($matrix)->flatten()->filter()->count() }}</h4>
                            <small class="text-muted">Assignations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
