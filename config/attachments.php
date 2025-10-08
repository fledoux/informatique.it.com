<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Taille maximale des fichiers
    |--------------------------------------------------------------------------
    |
    | Taille maximale autorisée pour les pièces jointes (en octets).
    | Par défaut : 20 Mo = 20971520 bytes
    |
    */
    'max_size' => 20971520, // 20 Mo

    /*
    |--------------------------------------------------------------------------
    | Types MIME autorisés
    |--------------------------------------------------------------------------
    |
    | Si vide, tous les types MIME sont autorisés SAUF les extensions bloquées.
    | Plus flexible et évite les problèmes avec des MIME types manquants.
    |
    */
    'allowed_mime_types' => [],

    /*
    |--------------------------------------------------------------------------
    | Extensions bloquées
    |--------------------------------------------------------------------------
    |
    | Extensions de fichiers dangereuses qui seront rejetées
    | même si le type MIME semble valide.
    |
    */
    'blocked_extensions' => [
        // Exécutables Windows
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'msi', 'dll',
        
        // Scripts
        'vbs', 'js', 'jar', 'ps1', 'psm1',
        
        // Shell scripts Unix/Linux
        'sh', 'bash', 'zsh', 'fish', 'csh',
        
        // Packages système
        'app', 'deb', 'rpm', 'dmg', 'pkg',
        
        // Fichiers PHP (sécurité)
        'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
    ],

    /*
    |--------------------------------------------------------------------------
    | Durée de validité des URLs signées
    |--------------------------------------------------------------------------
    |
    | Durée en minutes pendant laquelle une URL signée reste valide.
    | Par défaut : 5 minutes
    |
    */
    'temporary_url_minutes' => 5,
];
