<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
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

        // Manager et admin peuvent voir leur propre société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // Super-admin peut tout voir
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent voir uniquement leur propre société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seul super-admin peut créer des sociétés
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // Super-admin peut tout modifier
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent modifier uniquement leur propre société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Seul super-admin peut supprimer des sociétés
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->hasRole('super-admin');
    }
}
