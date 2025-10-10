<?php

return [
    'entity' => 'Message',
    'id' => 'ID',
    'List' => 'Liste des messages',
    'YourList' => 'Vos messages',
    'Edit' => 'Modifier',
    'Details' => 'Détails',
    'Actions' => 'Actions',
    'New' => 'Nouveau message',
    'Save' => 'Enregistrer',
    'Back' => 'Retour',
    'Delete' => 'Supprimer',
    'Delete?' => 'Supprimer ?',
    'No data' => 'Aucun message',
    
    'conversation_title' => 'Conversation',
    'reply_button' => 'Répondre',
    'internal_note_button' => 'Note interne',
    'public_messages' => 'Messages publics',
    'internal_messages' => 'Notes internes',

    'h1' => [
        'List' => '<i class="fa-regular fa-messages me-2"></i>Messages',
        'Create' => '<i class="fa-regular fa-plus me-2"></i>Nouveau message',
        'Edit' => '<i class="fa-regular fa-pen-to-square me-2"></i>Modifier le message',
        'Details' => '<i class="fa-regular fa-eye me-2"></i>Détails du message',
        'Reply' => '<i class="fa-regular fa-reply me-2"></i>Répondre',
        'InternalNote' => '<i class="fa-regular fa-lock me-2"></i>Note interne'
    ],

    'btn' => [
        'New' => '<i class="fa-regular fa-plus me-2"></i>Nouveau message',
        'Reply' => '<i class="fa-regular fa-reply me-2"></i>Répondre',
        'InternalNote' => '<i class="fa-regular fa-lock me-2"></i>Note interne'
    ],

    'fields' => [
        'status' => 'Statut',
        'subject' => 'Sujet',
        'body' => 'Message',
        'company_id' => 'Entreprise',
        'ticket_id' => 'Ticket',
        'author_id' => 'Auteur'
    ],
    
    'status' => [
        'active' => 'Public',
        'inactive' => 'Inactif',
        'internal' => 'Note interne'
    ],
    
    'statusBadgeColor' => [
        'active' => 'bg-success bg-opacity-75',
        'inactive' => 'bg-secondary bg-opacity-75',
        'internal' => 'bg-warning text-dark bg-opacity-75'
    ]
];
