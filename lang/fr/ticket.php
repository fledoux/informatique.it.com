<?php

return [
        'entity' => 'Ticket',
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
            'priority' => 'Priorité',
            'company_id' => 'Entreprise',
            'author_id' => 'Auteur',
            'assigned_to' => 'Assigné à',
            'assigned_at' => 'Date d\'assignation',
            'due' => 'Échéance',
            'folder_code' => 'Code dossier',
            'subject' => 'Sujet',
            'question' => 'Description',
            'billable' => 'Facturable'
    ],

    'enum' => [
            'status' => [
                'new' => 'Nouveau',
                'in_progress' => 'En cours',
                'waiting' => 'En attente',
                'resolved' => 'Résolu',
                'closed' => 'Fermé',
                'canceled' => 'Annulé'
            ],
            'priority' => [
                'low' => 'Faible',
                'normal' => 'Normal',
                'high' => 'Élevée',
                'urgent' => 'Urgent'
            ]
    ]
];
