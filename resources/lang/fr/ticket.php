<?php

return [
    'entity' => 'Support',
    'Id' => 'n°',
    'Status' => 'Statut',
    'Priority' => 'Priorité',
    'Company' => 'Entreprise',
    'Subject' => 'Sujet',
    'AssignedTo' => 'Assigné à',
    'DueAt' => 'Échéance',
    'FolderCode' => 'Code dossier',
    'Question' => 'Description',
    'Billable' => 'Prestation facturable',
    'Author' => 'Auteur',
    'AssignedAt' => 'Assigné le',
    'List' => 'Liste des demandes',
    'Answer' => '<i class="fa-regular fa-reply"></i> Répondre',
    'Note Interne' => '<i class="fa-regular fa-lock"></i> Note interne',
    'Resend Confirmation' => '<i class="fa-regular fa-envelope"></i> Renvoyer confirmation',
    'cgv' => 'a accepté(e) les Conditions générales d\'utilisation.',
    'yes' => 'Oui',
    'no' => 'Non',
    'ticket' => 'ticket',
    'tickets' => 'tickets',
    'Create' => 'Créer une demande de support',
    'Create at' => 'Créé le',

    'h1' => [
        'List' => '<i class="fa-regular fa-message-question me-2"></i>Demande de support',
        'Create' => '<i class="fa-regular fa-plus me-2"></i>Créer une demande de support',
        'Edit' => '<i class="fa-regular fa-pen-to-square me-2"></i>Modifier la demande de support',
        'Details' => '<i class="fa-regular fa-eye me-2"></i>Détails de la demande de support',
        'Merge' => '<i class="fa-regular fa-code-merge me-2"></i>Fusionner les tickets'
    ],

    'btn' => [
        'New' => '<i class="fa-regular fa-plus me-2"></i>Nouveau ticket',
        'Merge' => '<i class="fa-regular fa-code-merge me-1"></i>Fusionner'
    ],

    'merge' => [
        'title' => 'Fusionner les tickets',
        'keep' => 'Ticket à conserver',
        'delete' => 'Ticket à fusionner (sera supprimé)',
        'select' => 'Sélectionner le ticket à fusionner',
        'confirm' => 'Êtes-vous sûr de vouloir fusionner ces tickets ? Cette action est irréversible.',
        'success' => 'Le ticket #:id a été fusionné avec succès.',
        'info' => 'Cette opération va fusionner un autre ticket avec le ticket actuel. Tous les messages et pièces jointes de l\'ancien ticket seront transférés vers ce ticket, puis l\'ancien ticket sera supprimé.',
        'warning_title' => 'Attention',
        'warning_messages' => 'Tous les messages du ticket à fusionner seront transférés vers le ticket à conserver',
        'warning_attachments' => 'Toutes les pièces jointes seront également transférées',
        'warning_dates' => 'Les dates de création des messages et pièces jointes seront conservées',
        'warning_system_message' => 'Un message système sera créé pour indiquer la fusion',
        'warning_delete' => 'Le ticket à fusionner sera définitivement supprimé',
        'warning_irreversible' => 'Cette action est irréversible',
        'no_tickets' => 'Aucun ticket disponible pour la fusion.',
    ],

    'fields' => [
        'status' => 'Statut',
        'priority' => 'Priorité',
        'company_id' => 'Entreprise',
        'author_id' => 'Auteur',
        'assigned_to' => 'Assigné à',
        'assigned_at' => 'Assigné le',
        'due' => 'Échéance',
        'folder_code' => 'n° Dossier',
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
        'new' => 'bg-danger bg-opacity-75',
        'in_progress' => 'bg-primary bg-opacity-75',
        'waiting' => 'bg-warning text-dark bg-opacity-75',
        'resolved' => 'bg-success bg-opacity-75',
        'closed' => 'bg-secondary bg-opacity-75',
        'canceled' => 'bg-danger bg-opacity-75'
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
        'low' => 'bg-success bg-opacity-75',
        'normal' => 'bg-primary bg-opacity-75',
        'high' => 'bg-warning text-dark bg-opacity-75',
        'urgent' => 'bg-danger bg-opacity-75'
    ],
    'billable' => [
        'yes' => '<i class="fa-solid fa-square-check text-success"></i>',
        'no' => '<i class="fa-solid fa-square-xmark text-danger"></i>'
    ],
    'billableindex' => [
        'yes' => '<i class="fa-solid fa-check text-success"></i>',
        'no' => '<i class="fa-solid fa-square-xmark text-danger"></i>'
    ],
    'empty_state' => [
        'title' => 'Bienvenue sur votre espace support',
        'create_button' => 'Créer ma première demande'
    ],
];
