<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration IMAP pour la récupération d'emails
    |--------------------------------------------------------------------------
    |
    | Configuration pour se connecter à un serveur IMAP et récupérer les
    | emails pour les convertir en tickets du système helpdesk.
    |
    */

    'host' => env('IMAP_HOST'),
    'port' => env('IMAP_PORT', 993),
    'username' => env('IMAP_USERNAME'),
    'password' => env('IMAP_PASSWORD'),
    'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
    'folder' => env('IMAP_FOLDER', 'INBOX'),
    'folder_separator' => env('IMAP_FOLDER_SEPARATOR', '.'),

    /*
    |--------------------------------------------------------------------------
    | Options de traitement
    |--------------------------------------------------------------------------
    */

    'mark_as_read' => env('IMAP_MARK_AS_READ', true),
    'delete_after_processing' => env('IMAP_DELETE_AFTER_PROCESSING', false),
    'max_emails_per_run' => env('IMAP_MAX_EMAILS_PER_RUN', 50),
    
    /*
    |--------------------------------------------------------------------------
    | Options d'archivage
    |--------------------------------------------------------------------------
    */
    
    'move_to_archive' => env('IMAP_MOVE_TO_ARCHIVE', true),
    'archive_folder_pattern' => env('IMAP_ARCHIVE_FOLDER_PATTERN', 'Archives/{year}'),

    /*
    |--------------------------------------------------------------------------
    | Entreprise par défaut pour les nouveaux utilisateurs
    |--------------------------------------------------------------------------
    */

    'default_company_name' => env('IMAP_DEFAULT_COMPANY_NAME', 'Clients Email'),

    /*
    |--------------------------------------------------------------------------
    | Priorité par défaut pour les tickets créés depuis les emails
    |--------------------------------------------------------------------------
    */

    'default_priority' => env('IMAP_DEFAULT_PRIORITY', 'normal'),

];