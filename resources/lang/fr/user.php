<?php

return [
        'entity' => 'User',
        'id' => 'ID',
        'List' => 'Liste',
        'Edit' => 'Modifier',
        'Details' => 'Détails',
        'Actions' => 'Actions',
        'New' => 'Nouveau',
        'Save' => 'Enregistrer',
        'Back' => 'Retour',
        'Delete' => 'Supprimer',
        'Delete?' => 'Supprimer ?',
        'No data' => 'Aucune donnée',

    'fields' => [
            'name' => 'Nom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'status' => 'Statut',
            'company_id' => 'Entreprise',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'initial' => 'Initial',
            'phone' => 'Téléphone',
            'last_login' => 'Last Login',
            'agree_terms' => 'Agree Terms',
            'channels' => 'Channels',
            'channels_email' => 'Channels Email',
            'channels_sms' => 'Channels Sms',
            'note' => 'Note',
            'roles' => 'Rôles'
    ],

    'status' => [
            'active' => 'Actif',
            'inactive' => 'Inactif'
    ],
    'agree_terms' => [
            'oui' => 'Oui',
            'non' => 'Non'
    ],

    'roles' => [
        'super-admin' => 'Super Administrateur',
        'manager' => 'Gestionnaire',
        'user' => 'Utilisateur'
    ]
];
