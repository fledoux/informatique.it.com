<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
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

        // Manager et admin peuvent voir les tickets de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Super-admin peut tout voir
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent voir uniquement les tickets de leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id === $ticket->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Super-admin peut créer des tickets pour toutes les sociétés
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Manager et admin peuvent créer des tickets pour leur société
        return $user->hasAnyRole(['manager', 'admin']) && $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Seul super-admin peut supprimer des tickets
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Seul super-admin peut supprimer des tickets
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('super-admin');
    }
}
