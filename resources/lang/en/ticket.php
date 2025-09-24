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
        'status' => 'Status',
        'priority' => 'Priority',
        'company_id' => 'Company Id',
        'author_id' => 'Author Id',
        'assigned_to' => 'Assigned To',
        'assigned_at' => 'Assigned At',
        'due' => 'Due',
        'folder_code' => 'Folder Code',
        'subject' => 'Subject',
        'question' => 'Question',
        'billable' => 'Billable'
    ],

    'status' => [
        'new' => 'New',
        'in_progress' => 'In_progress',
        'waiting' => 'Waiting',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'canceled' => 'Canceled'
    ],
    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent'
    ]
];
