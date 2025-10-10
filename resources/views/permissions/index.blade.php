@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            <i class="fa-regular fa-shield-halved me-2"></i>
            Gestion des Permissions
        </h1>
        <div class="d-flex gap-1 gap-sm-2">
            @can('permission.edit')
                <a href="{{ route('permissions.matrix') }}" class="btn btn-outline-primary">
                    <i class="fa-regular fa-table-cells me-2"></i>
                    Matrice
                </a>
            @endcan
            @can('permission.create')
                <a href="{{ route('permissions.create') }}" class="btn btn-orange">
                    <i class="fa-regular fa-plus me-2"></i>
                    Nouvelle
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
                    <span class="badge bg-primary bg-opacity-75">{{ count($groupPermissions) }}</span>
                    {{ ucfirst($group) }}
                </h5>
            </div>
            <div class="card-body p-2 p-sm-3">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th class="align-middle" style="width: 40%">Rôles</th>
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
                                <tr class="{{ !$isSystem ? 'table-warning' : '' }}">
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
                                            <span class="badge bg-warning">
                                                <i class="fa-regular fa-pencil"></i>
                                                Custom
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $rolesCount = $permission->roles()->count();
                                            $badgeClass = $rolesCount == 0 ? 'bg-secondary bg-opacity-75' : ($rolesCount == 1 ? 'bg-success bg-opacity-75' : 'bg-danger bg-opacity-75');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $rolesCount }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            // Compter les utilisateurs via leurs rôles qui ont cette permission
                                            $usersCount = $permission->roles()->with('users')->get()->pluck('users')->flatten()->unique('id')->count();
                                            $usersBadgeClass = $usersCount == 0 ? 'bg-secondary bg-opacity-75' : ($usersCount == 1 ? 'bg-success bg-opacity-75' : 'bg-danger bg-opacity-75');
                                        @endphp
                                        <span class="badge {{ $usersBadgeClass }}">{{ $usersCount }}</span>
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
                                                @php
                                                    $usersCountForDeletion = $permission->roles()->with('users')->get()->pluck('users')->flatten()->unique('id')->count();
                                                @endphp
                                                @if($permission->roles()->count() == 0 && $usersCountForDeletion == 0)
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

@if (!empty($grouped))
    @push('javascripts')
        @include('partials._datatable')
    @endpush
@endif
