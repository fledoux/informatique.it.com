<?php

return [
        'entity' => 'Ticket',
        'Id' => 'No.',
        'Status' => 'Status',
        'Priority' => 'Priority',
        'Company' => 'Company',
        'Subject' => 'Subject',
        'AssignedTo' => 'Assigned to',
        'DueAt' => 'Due date',
        'FolderCode' => 'Folder code',
        'Question' => 'Description',
        'Billable' => 'Billable',
        'Author' => 'Author',
        'AssignedAt' => 'Assignment date',

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
