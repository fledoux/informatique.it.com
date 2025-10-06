<?php

return [
    'entity' => 'Message',
    'id' => 'ID',
    'List' => 'Liste des messages',
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
