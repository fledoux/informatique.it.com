<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
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

        // Manager et admin peuvent voir les contacts de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contact $contact): bool
    {
        // Super-admin peut tout voir
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent voir uniquement les contacts de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $contact->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super-admin peut créer des contacts pour toutes les sociétés
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent créer des contacts pour leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contact $contact): bool
    {
        // Super-admin peut tout modifier
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent modifier uniquement les contacts de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $contact->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contact $contact): bool
    {
        // Super-admin peut supprimer n'importe quel contact
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent supprimer les contacts de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $contact->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contact $contact): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $contact->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contact $contact): bool
    {
        return $user->hasRole('super-admin');
    }
}
