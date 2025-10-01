@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fa-regular fa-shield-halved"></i>
            Gestion des Permissions
        </h1>
        <div>
            @can('permission.edit')
                <a href="{{ route('permissions.matrix') }}" class="btn btn-outline-primary">
                    <i class="fa-regular fa-table-cells"></i>
                    Matrice Permissions/Rôles
                </a>
            @endcan
            @can('permission.create')
                <a href="{{ route('permissions.create') }}" class="btn btn-orange">
                    <i class="fa-regular fa-square-plus"></i>
                    Nouvelle Permission Custom
                </a>
            @endcan
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fa-regular fa-circle-info"></i>
        <strong>Permissions Système</strong> : Utilisées dans le code, protégées contre la modification/suppression.<br>
        <strong>Permissions Custom</strong> : Créées par vous, modifiables et supprimables.
    </div>

    @foreach($grouped as $group => $groupPermissions)
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
                                <th class="align-middle" style="width: 40%">Permission</th>
                                <th class="text-center align-middle" style="width: 15%">Type</th>
                                <th class="text-center align-middle" style="width: 15%">Pages avec Rôle</th>
                                <th class="text-center align-middle" style="width: 15%">Utilisateurs</th>
                                <th class="text-center align-middle" style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupPermissions as $permission)
                                @php
                                    $isSystem = \App\Helpers\PermissionHelper::isSystemPermission($permission->name);
                                @endphp
                                <tr class="{{ !$isSystem ? 'table-success' : '' }}">
                                    <td>
                                        <code class="text-dark">{{ $permission->name }}</code>
                                    </td>
                                    <td class="text-center">
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
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-75">{{ $permission->roles()->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-75">{{ $permission->users()->count() }}</span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @can('permission.show')
                                            <a href="{{ route('permissions.show', $permission) }}" 
                                               class="btn btn-link text-decoration-none p-0 me-2"
                                               title="Détails">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endcan
                                        
                                        @if(!$isSystem)
                                            @can('permission.edit')
                                                <a href="{{ route('permissions.edit', $permission) }}" 
                                                   class="btn btn-link text-decoration-none p-0 me-2"
                                                   title="Modifier">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('permission.delete')
                                                @if($permission->roles()->count() == 0 && $permission->users()->count() == 0)
                                                    <form method="POST" 
                                                          action="{{ route('permissions.destroy', $permission) }}" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Supprimer cette permission ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-link text-decoration-none text-danger p-0"
                                                                title="Supprimer">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted" title="Permission assignée, suppression impossible">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </span>
                                                @endif
                                            @endcan
                                        @else
                                            <span class="text-muted small"><i class="fa-regular fa-lock"></i></span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-4">
        <p class="text-muted">
            <i class="fa-regular fa-lightbulb"></i>
            <strong>Astuce :</strong> Utilisez la <a href="{{ route('permissions.matrix') }}">matrice permissions/rôles</a> 
            pour gérer rapidement les accès de chaque rôle.
        </p>
    </div>
@endsection
