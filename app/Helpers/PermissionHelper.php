<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Liste des permissions système (protégées, non modifiables)
     * 
     * @return array
     */
    public static function getSystemPermissions(): array
    {
        return [
            // Allow Domain Registration
            'allowdomain.index',
            'allowdomain.show',
            'allowdomain.create',
            'allowdomain.edit',
            'allowdomain.delete',
            
            // Company
            'company.index',
            'company.show',
            'company.create',
            'company.edit',
            'company.delete',
            
            // Contact
            'contact.index',
            'contact.show',
            'contact.create',
            'contact.edit',
            'contact.delete',
            
            // Locale
            'locale.index',
            'locale.show',
            'locale.create',
            'locale.edit',
            'locale.delete',
            
            // Page
            'page.index',
            'page.show',
            'page.create',
            'page.edit',
            'page.delete',
            
            // Permission
            'permission.index',
            'permission.show',
            'permission.create',
            'permission.edit',
            'permission.delete',
            
            // Test
            'test.index',
            'test.show',
            'test.create',
            'test.edit',
            'test.delete',
            
            // Ticket
            'ticket.index',
            'ticket.show',
            'ticket.create',
            'ticket.edit',
            'ticket.delete',
            
            // User
            'user.index',
            'user.show',
            'user.create',
            'user.edit',
            'user.delete',
            
            // Generic
            'admin.access',
            'reports.access',
        ];
    }

    /**
     * Vérifie si une permission est une permission système (protégée)
     * 
     * @param string $permissionName
     * @return bool
     */
    public static function isSystemPermission(string $permissionName): bool
    {
        return in_array($permissionName, self::getSystemPermissions());
    }

    /**
     * Vérifie si une permission est custom (modifiable)
     * 
     * @param string $permissionName
     * @return bool
     */
    public static function isCustomPermission(string $permissionName): bool
    {
        return !self::isSystemPermission($permissionName);
    }

    /**
     * Obtient le groupe d'une permission (ex: 'company' pour 'company.index')
     * 
     * @param string $permissionName
     * @return string
     */
    public static function getPermissionGroup(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        return $parts[0] ?? 'custom';
    }

    /**
     * Obtient l'action d'une permission (ex: 'index' pour 'company.index')
     * 
     * @param string $permissionName
     * @return string
     */
    public static function getPermissionAction(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        return $parts[1] ?? '';
    }

    /**
     * Groupe les permissions par catégorie
     * 
     * @param \Illuminate\Support\Collection $permissions
     * @return array
     */
    public static function groupPermissions($permissions): array
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $group = self::getPermissionGroup($permission->name);
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $permission;
        }
        
        ksort($grouped);
        return $grouped;
    }
}
