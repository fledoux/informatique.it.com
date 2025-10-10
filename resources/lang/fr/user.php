<?php

return [
        'entity' => 'User',
        'id' => 'n°',
        'List' => 'Liste des utilisateurs',
        'Edit' => 'Modifier',
        'Details' => 'Détails',
        'TitleDetails' => '<i class="far fa-eye"></i> Détails du compte',
        'Actions' => 'Actions',
        'New' => 'Nouveau',
        'Save' => 'Enregistrer',
        'Back' => 'Retour',
        'Delete' => 'Supprimer',
        'Delete?' => 'Supprimer ?',
        'No data' => 'Aucune donnée',
        'Create' => 'Ajouter un utilisateur',

    'h1' => [
        'List' => '<i class="fa-regular fa-users me-2"></i>Utilisateurs',
        'Create' => '<i class="fa-regular fa-plus me-2"></i>Créer un utilisateur',
        'Edit' => '<i class="fa-regular fa-pen-to-square me-2"></i>Modifier',
        'Details' => '<i class="fa-regular fa-eye me-2"></i>Détails du compte'
    ],

    'fields' => [
                'name' => 'Nom du compte',
                'email' => 'Email',
                'password' => 'Mot de passe',
                'status' => 'Statut',
                'company_id' => 'Entreprise',
                'firstname' => 'Prénom',
                'lastname' => 'Nom de famille',
                'initial' => 'Initiale',
                'phone' => 'Téléphone',
                'last_login' => 'Dernière connexion',
                'agree_terms' => 'Accepter les conditions',
                'Conditions' => 'CGU',
                'channels' => 'Communications',
                'channels_email' => 'Email',
                'channels_sms' => 'Sms',
                'note' => 'Note',
                'roles' => 'Rôles'
        ],
        'btn' => [
                'New' => '<i class="fa fa-plus"></i> Ajouter un utilisateur'
        ],
        'status' => [
                'active' => 'Actif',
                'inactive' => 'Inactif'
        ],
        'statusBadgeColor' => [
                'active' => 'bg-success',
                'inactive' => 'bg-secondary'
        ],
        'agree_terms' => [
                'oui' => 'Oui',
                'non' => 'Non'
        ],
        'statusAgreeTermsColor' => [
                'oui' => '<i class="fa-regular fa-square-check text-success"></i>',
                'non' => '<i class="fa-regular fa-square-xmark text-danger"></i>'
        ],

        'roles' => [
                'super-admin' => 'Super Admin',
                'manager' => 'Gestionnaire',
                'user' => 'Utilisateur',
                'admin' => 'Admin'
        ],
        'badgeRolesColor' => [
                'super-admin' => 'bg-danger',
                'admin' => 'bg-orange',
                'manager' => 'bg-warning',
                'user' => 'bg-success'
        ],
        'channels' => [
                'email' => 'Email',
                'sms' => 'Sms'
        ],
];
