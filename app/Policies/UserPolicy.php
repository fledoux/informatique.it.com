<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super-admin peut tout voir
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent voir les utilisateurs de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Super-admin peut tout voir
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent voir uniquement les utilisateurs de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seul super-admin peut créer des utilisateurs
        // Les managers/admins doivent utiliser le formulaire d'inscription public
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Super-admin peut tout modifier
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent modifier uniquement les utilisateurs de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Super-admin peut supprimer n'importe quel utilisateur
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent supprimer des utilisateurs de leur société (sauf eux-mêmes)
        return $user->hasAnyRole(['manager', 'admin']) 
            && $user->company_id === $model->company_id 
            && $user->id !== $model->id; // Ne peut pas se supprimer soi-même
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Seul super-admin peut supprimer définitivement
        return $user->hasRole('super-admin');
    }
}
