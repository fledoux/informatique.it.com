<?php

namespace App\Helpers;

class Helper
{
    /**
     * Génère les initiales à partir du prénom et nom
     */
    public static function generateInitials(string $firstname, string $lastname): string
    {
        $firstInitial = !empty($firstname) ? strtoupper(mb_substr(trim($firstname), 0, 1)) : '';
        $lastInitial = !empty($lastname) ? strtoupper(mb_substr(trim($lastname), 0, 1)) : '';
        
        return $firstInitial . $lastInitial;
    }

    /**
     * Formate le nom complet d'une personne
     */
    public static function getFullName(?string $firstname, ?string $lastname, ?string $name = null): string
    {
        $fullName = trim(($firstname ?? '') . ' ' . ($lastname ?? ''));
        
        if (!empty($fullName)) {
            return $fullName;
        }
        
        return $name ?? '';
    }

    /**
     * Formate un numéro de téléphone
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        
        // Supprime tous les espaces, points, tirets
        $cleaned = preg_replace('/[\s\-\.]/', '', $phone);
        
        // Formate si c'est un numéro français (10 chiffres commençant par 0)
        if (preg_match('/^0[1-9]\d{8}$/', $cleaned)) {
            return substr($cleaned, 0, 2) . ' ' . substr($cleaned, 2, 2) . ' ' . 
                   substr($cleaned, 4, 2) . ' ' . substr($cleaned, 6, 2) . ' ' . 
                   substr($cleaned, 8, 2);
        }
        
        return $phone; // Retourne le format original si pas reconnu
    }

    /**
     * Génère une couleur de badge selon le statut
     */
    public static function getStatusBadgeColor(string $status, array $mapping = []): string
    {
        $defaultMapping = [
            'active' => 'success',
            'inactive' => 'secondary',
            'new' => 'info',
            'in_progress' => 'primary',
            'waiting' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            'canceled' => 'danger',
            'low' => 'info',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger'
        ];
        
        $colors = array_merge($defaultMapping, $mapping);
        
        return $colors[$status] ?? 'secondary';
    }
}