<?php

return [
    'entity' => 'Ticket',
    'id' => 'ID',
    'List' => 'List',
    'Edit' => 'Edit',
    'Details' => 'Details',
    'Actions' => 'Actions',
    'New' => 'New',
    'Save' => 'Save',
    'Back' => 'Back',
    'Delete' => 'Delete',
    'Delete?' => 'Delete?',
    'No data' => 'No data',

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
