<?php

return [
    'entity' => 'Ticket',
    'Id' => 'N°',
    'Status' => 'Statut',
    'Priority' => 'Priorité',
    'Company' => 'Entreprise', 
    'Subject' => 'Sujet',
    'AssignedTo' => 'Assigné à',
    'DueAt' => 'Échéance',
    'FolderCode' => 'Code dossier',
    'Question' => 'Description',
    'Billable' => 'Facturable',
    'Author' => 'Auteur',
    'AssignedAt' => 'Date d\'assignation',

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
];
