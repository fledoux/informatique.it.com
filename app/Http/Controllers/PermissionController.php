<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('permission:permission.index')->only('index');
        $this->middleware('permission:permission.create')->only(['create', 'store']);
        $this->middleware('permission:permission.edit')->only(['edit', 'update', 'updateMatrix']);
        $this->middleware('permission:permission.delete')->only('destroy');
    }

    /**
     * Afficher la liste des permissions avec indication système/custom
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        $grouped = PermissionHelper::groupPermissions($permissions);
        
        return view('permissions.index', compact('permissions', 'grouped'));
    }

    /**
     * Afficher le formulaire de création d'une permission custom
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Créer une nouvelle permission custom
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name|regex:/^[a-z0-9._-]+$/',
            'description' => 'nullable|string|max:500',
        ], [
            'name.regex' => 'Le nom ne peut contenir que des lettres minuscules, chiffres, points, tirets et underscores.',
            'name.unique' => 'Cette permission existe déjà.',
        ]);

        // Vérifier que ce n'est pas une permission système
        if (PermissionHelper::isSystemPermission($request->name)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Impossible de créer une permission système. Utilisez un préfixe custom.');
        }

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('permissions.index')
            ->with('success', __('global.messages.created'));
    }

    /**
     * Afficher les détails d'une permission
     */
    public function show(Permission $permission)
    {
        $roles = $permission->roles;
        $users = $permission->users;
        $isSystem = PermissionHelper::isSystemPermission($permission->name);
        
        return view('permissions.show', compact('permission', 'roles', 'users', 'isSystem'));
    }

    /**
     * Afficher le formulaire d'édition (seulement pour permissions custom)
     */
    public function edit(Permission $permission)
    {
        if (PermissionHelper::isSystemPermission($permission->name)) {
            return redirect()->route('permissions.index')
                ->with('error', 'Les permissions système ne peuvent pas être modifiées.');
        }

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Mettre à jour une permission custom
     */
    public function update(Request $request, Permission $permission)
    {
        if (PermissionHelper::isSystemPermission($permission->name)) {
            return redirect()->route('permissions.index')
                ->with('error', 'Les permissions système ne peuvent pas être modifiées.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id . '|regex:/^[a-z0-9._-]+$/',
        ], [
            'name.regex' => 'Le nom ne peut contenir que des lettres minuscules, chiffres, points, tirets et underscores.',
        ]);

        $permission->update([
            'name' => $request->name,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', __('global.messages.updated'));
    }

    /**
     * Supprimer une permission custom
     */
    public function destroy(Permission $permission)
    {
        if (PermissionHelper::isSystemPermission($permission->name)) {
            return redirect()->route('permissions.index')
                ->with('error', 'Les permissions système ne peuvent pas être supprimées.');
        }

        // Vérifier si la permission est utilisée
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cette permission est assignée à des rôles ou utilisateurs. Retirez-la d\'abord.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', __('global.messages.deleted'));
    }

    /**
     * Afficher la matrice d'attribution permissions/rôles
     */
    public function matrix()
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('name')->get();
        $grouped = PermissionHelper::groupPermissions($permissions);
        
        // Créer une matrice des permissions par rôle
        $matrix = [];
        foreach ($permissions as $permission) {
            foreach ($roles as $role) {
                $matrix[$permission->name][$role->name] = $role->hasPermissionTo($permission->name);
            }
        }
        
        return view('permissions.matrix', compact('roles', 'permissions', 'grouped', 'matrix'));
    }

    /**
     * Mettre à jour la matrice des permissions
     */
    public function updateMatrix(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $roles = Role::all();
            $permissions = Permission::all();
            
            // Récupérer les permissions cochées depuis le formulaire
            $checkedPermissions = $request->get('permissions', []);
            
            // Pour chaque rôle, synchroniser ses permissions
            foreach ($roles as $role) {
                $rolePermissions = [];
                
                foreach ($permissions as $permission) {
                    $key = $permission->name . '_' . $role->name;
                    if (isset($checkedPermissions[$key])) {
                        $rolePermissions[] = $permission->name;
                    }
                }
                
                // Synchroniser les permissions du rôle
                $role->syncPermissions($rolePermissions);
            }
            
            DB::commit();
            
            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            return redirect()->route('permissions.matrix')
                ->with('success', __('global.messages.updated'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('permissions.matrix')
                ->with('error', 'Erreur lors de la mise à jour des permissions: ' . $e->getMessage());
        }
    }
}