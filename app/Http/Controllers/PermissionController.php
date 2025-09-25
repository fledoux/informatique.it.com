<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Afficher la matrice des permissions
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        
        // Créer une matrice des permissions par rôle
        $matrix = [];
        foreach ($permissions as $permission) {
            foreach ($roles as $role) {
                $matrix[$permission->name][$role->name] = $role->hasPermissionTo($permission->name);
            }
        }
        
        return view('permissions.index', compact('roles', 'permissions', 'matrix'));
    }

    /**
     * Mettre à jour les permissions
     */
    public function update(Request $request)
    {
        try {
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
            
            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            return redirect()->route('permissions.index')
                ->with('success', __('global.messages.updated'));
                
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')
                ->with('error', 'Erreur lors de la mise à jour des permissions: ' . $e->getMessage());
        }
    }
}