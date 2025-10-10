<?php

return [
        'entity' => 'Company',
        'id' => 'ID',
        'List' => 'Société',
        'YourList' => 'Liste des sociétés',
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
            'status' => 'Statut',
            'name' => 'Nom',
            'siret' => 'Siret',
            'vat_number' => 'Vat Number',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'website' => 'Site web',
            'address_line1' => 'Adresse',
            'address_line2' => 'Suite',
            'zip' => 'CP',
            'city' => 'Ville',
            'country' => 'Pays',
            'notes' => 'Notes'
    ],

    'enum' => [
            'status' => [
                'active' => 'Actif',
                'inactive' => 'Inactif'
            ]
    ],

    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif'
    ],

    'statusBadgeColor' => [
        'active' => 'bg-success',
        'inactive' => 'bg-secondary'
    ]
];
