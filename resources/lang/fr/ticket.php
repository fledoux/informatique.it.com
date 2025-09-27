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
    'AssignedAt' => 'Assigné le',
    'List' => 'Liste des tickets',
    'Answer' => '<i class="fa-regular fa-square-plus"></i> Répondre',
    'cgv' => 'a accepté(e) les Conditions générales d\'utilisation.',
    'yes' => 'Oui',
    'no' => 'Non',
    'ticket' => 'ticket',
    'tickets' => 'tickets',

    'fields' => [
        'status' => 'Statut',
        'priority' => 'Priorité',
        'company_id' => 'Entreprise',
        'author_id' => 'Auteur',
        'assigned_to' => 'Assigné à',
        'assigned_at' => 'Assigné le',
        'due' => 'Échéance',
        'folder_code' => 'N° Dossier',
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
    'statusBadgeColor' => [
        'new' => 'bg-danger',
        'in_progress' => 'bg-primary',
        'waiting' => 'bg-warning',
        'resolved' => 'bg-success',
        'closed' => 'bg-secondary',
        'canceled' => 'bg-danger'
    ],
    'priority' => [
        'low' => 'Faible',
        'normal' => 'Normal',
        'high' => 'Élevée',
        'urgent' => 'Urgent'
    ],
    'priorityFull' => [
        'low' => 'Priorité Faible',
        'normal' => 'Priorité Normale',
        'high' => 'Priorité Élevée',
        'urgent' => 'Priorité Urgente'
    ],
    'priorityBadgeColor' => [
        'low' => 'bg-success',
        'normal' => 'bg-primary',
        'high' => 'bg-warning',
        'urgent' => 'bg-danger'
    ],
    'billable' => [
        'yes' => '<i class="fa-regular fa-square-check text-success"></i>',
        'no' => '<i class="fa-regular fa-square-xmark text-danger"></i>'
    ],
];
