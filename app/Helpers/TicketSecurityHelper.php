<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;

class TicketSecurityHelper
{
    /**
     * Vérifier si un utilisateur peut accéder à un ticket
     */
    public static function canAccessTicket(User $user, Ticket $ticket): bool
    {
        // Super-admin : accès à tous les tickets
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        // Autres rôles : seulement les tickets de leur société
        return $ticket->company_id === $user->company_id;
    }
    
    /**
     * Vérifier si un utilisateur peut créer une note interne
     */
    public static function canCreateInternalNote(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
    
    /**
     * Vérifier si un utilisateur peut voir les notes internes
     */
    public static function canViewInternalNotes(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
    
    /**
     * Vérifier si un utilisateur peut gérer (modifier/supprimer) un message de ticket
     */
    public static function canManageTicketMessage(User $user, $ticketMessage): bool
    {
        // Super-admin : peut gérer tous les messages
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        // Autres rôles : peuvent gérer uniquement leurs propres messages dans leur société
        return $ticketMessage->author_id === $user->id && 
               $ticketMessage->company_id === $user->company_id;
    }
    
    /**
     * Obtenir un message d'erreur d'accès refusé
     */
    public static function getAccessDeniedMessage(string $action = 'accéder'): string
    {
        return "Vous ne pouvez pas {$action} aux tickets d'une autre société.";
    }
}