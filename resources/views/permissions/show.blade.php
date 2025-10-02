@extends('layouts.app')

@section('title', 'Détails de la Permission')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fa-regular fa-eye"></i>
            Détails de la Permission
        </h1>
        <div>
            @if(!$isSystem)
                @can('permission.edit')
                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-orange">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Modifier
                    </a>
                @endcan
            @endif
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-circle-info"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Nom :</th>
                                <td><code class="text-dark">{{ $permission->name }}</code></td>
                            </tr>
                            <tr>
                                <th>Type :</th>
                                <td>
                                    @if($isSystem)
                                        <span class="badge bg-primary">
                                            <i class="fa-solid fa-lock"></i>
                                            Permission Système
                                        </span>
                                        <br>
                                        <small class="text-muted">Cette permission est utilisée dans le code et ne peut pas être modifiée ou supprimée.</small>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-pencil"></i>
                                            Permission Custom
                                        </span>
                                        <br>
                                        <small class="text-muted">Cette permission a été créée manuellement et peut être modifiée.</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Groupe :</th>
                                <td>{{ ucfirst(\App\Helpers\PermissionHelper::getPermissionGroup($permission->name)) }}</td>
                            </tr>
                            <tr>
                                <th>Action :</th>
                                <td>{{ \App\Helpers\PermissionHelper::getPermissionAction($permission->name) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Guard :</th>
                                <td><code>{{ $permission->guard_name }}</code></td>
                            </tr>
                            <tr>
                                <th>Créée le :</th>
                                <td>{{ $permission->created_at ? $permission->created_at->format('d/m/Y à H\hi') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Modifiée le :</th>
                                <td>{{ $permission->updated_at ? $permission->updated_at->format('d/m/Y à H\hi') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-chart-simple"></i>
                        Statistiques d'utilisation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <h2 class="mb-0 text-orange">{{ $roles->count() }}</h2>
                                <small class="text-muted">Rôle{{ $roles->count() > 1 ? 's' : '' }} assigné{{ $roles->count() > 1 ? 's' : '' }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <h2 class="mb-0 text-orange">{{ $users->count() }}</h2>
                                <small class="text-muted">Utilisateur{{ $users->count() > 1 ? 's' : '' }} direct{{ $users->count() > 1 ? 's' : '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($roles->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa-solid fa-user-tag"></i>
                    Rôles ayant cette permission ({{ $roles->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rôle</th>
                                <th>Utilisateurs</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst($role->name) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $role->users()->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('permissions.matrix') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-table-cells"></i>
                                            Voir la matrice
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($users->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa-solid fa-users"></i>
                    Utilisateurs ayant cette permission directement ({{ $users->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @can('user.show')
                                            <a href="{{ route('user.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-regular fa-eye"></i>
                                                Voir l'utilisateur
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if(!$isSystem && $roles->count() == 0 && $users->count() == 0)
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Cette permission n'est assignée à aucun rôle ni utilisateur.
            @can('permission.delete')
                Vous pouvez la <a href="#" onclick="event.preventDefault(); document.getElementById('delete-form').submit();">supprimer</a> si elle n'est plus nécessaire.
                
                <form id="delete-form" method="POST" action="{{ route('permissions.destroy', $permission) }}" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
        </div>
    @endif

    @if(!$isSystem)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa-solid fa-code"></i>
                    Utilisation dans le code
                </h5>
            </div>
            <div class="card-body">
                <p><strong>Dans les vues Blade :</strong></p>
                <pre><code>@@can('{{ $permission->name }}')
    &lt;!-- Contenu visible uniquement avec cette permission --&gt;
@@endcan</code></pre>

                <p class="mt-3"><strong>Dans les contrôleurs :</strong></p>
                <pre><code>public function __construct()
{
    $this->middleware('permission:{{ $permission->name }}')->only('methodName');
}</code></pre>

                <p class="mt-3"><strong>Dans le code PHP :</strong></p>
                <pre><code>if (auth()->user()->can('{{ $permission->name }}')) {
    {{-- Code exécuté si l'utilisateur a la permission --}}
}</code></pre>
            </div>
        </div>
    @endif
@endsection
