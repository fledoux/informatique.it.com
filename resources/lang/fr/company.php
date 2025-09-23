<?php

return [
        'entity' => 'Company',
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
            'status' => 'Statut',
            'name' => 'Nom',
            'siret' => 'Siret',
            'vat_number' => 'Vat Number',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'website' => 'Site web',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'zip' => 'Code postal',
            'city' => 'Ville',
            'country' => 'Pays',
            'notes' => 'Notes'
    ],

    'enum' => [
            'status' => [
                'active' => 'Actif',
                'inactive' => 'Inactif'
            ]
    ]
];
