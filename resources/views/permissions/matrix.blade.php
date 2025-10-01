@extends('layouts.app')

@section('title', 'Matrice Permissions / Rôles')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fa-solid fa-table-cells"></i>
            Matrice Permissions / Rôles
        </h1>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour à la liste
        </a>
    </div>

    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info"></i>
        Cochez les permissions pour chaque rôle, puis cliquez sur <strong>Enregistrer</strong>.
    </div>

    <form method="POST" action="{{ route('permissions.matrix.update') }}">
        @csrf
        
        @foreach ($grouped as $group => $groupPermissions)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-regular fa-folder-open"></i>
                        {{ ucfirst($group) }}
                        <span class="badge bg-primary bg-opacity-75">{{ count($groupPermissions) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th class="text-start align-middle" style="min-width: 250px;">Permission</th>
                                    <th class="text-center align-middle" style="width: 120px;">Type</th>
                                    @foreach ($roles as $role)
                                        <th class="text-center align-middle" style="width: 150px;">
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="fw-bold">{{ ucfirst($role->name) }}</span>
                                                <small class="text-muted">
                                                    {{ $role->users()->count() }} 
                                                    {{ $role->users()->count() > 1 ? 'utilisateurs' : 'utilisateur' }}
                                                </small>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach ($groupPermissions as $permission)
                                    @php
                                        $isSystem = \App\Helpers\PermissionHelper::isSystemPermission($permission->name);
                                    @endphp
                                    <tr class="{{ !$isSystem ? 'table-success' : '' }}">
                                        <td class="align-middle">
                                            <code class="text-dark">{{ $permission->name }}</code>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($isSystem)
                                                <span class="badge bg-primary bg-opacity-75">
                                                    <i class="fa-regular fa-lock"></i>
                                                    Système
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fa-regular fa-pencil"></i>
                                                    Custom
                                                </span>
                                            @endif
                                        </td>
                                        @foreach ($roles as $role)
                                            <td class="text-center align-middle">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        name="permissions[{{ $permission->name }}_{{ $role->name }}]" 
                                                        id="perm_{{ $permission->id }}_{{ $role->id }}"
                                                        {{ $matrix[$permission->name][$role->name] ? 'checked' : '' }}
                                                        value="1"
                                                    >
                                                    <label class="form-check-label visually-hidden" for="perm_{{ $permission->id }}_{{ $role->id }}">
                                                        {{ $permission->name }} pour {{ $role->name }}
                                                    </label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i>
                Annuler
            </a>
            @can('permission.edit')
                <button type="submit" class="btn btn-orange">
                    <i class="fa-solid fa-save"></i>
                    Enregistrer les modifications
                </button>
            @endcan
        </div>
    </form>

    <div class="mt-4">
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <strong>Attention :</strong> Les modifications affectent immédiatement tous les utilisateurs ayant ces rôles.
            Le cache des permissions sera automatiquement vidé.
        </div>
    </div>
@endsection

@push('styles')
<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
        cursor: pointer;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255, 140, 0, 0.05);
    }
</style>
@endpush
