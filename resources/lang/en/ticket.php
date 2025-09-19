<?php

return [
    'Id' => 'ID',
    'Ticket_status' => 'Status',
    'Priority' => 'Priority',
    'Subject' => 'Subject',
    'Company' => 'Company',
    'AssignedTo' => 'Assigned To',
    'DueAt' => 'Due Date',
    
    'status' => [
        'new' => 'New',
        'in_progress' => 'In Progress', 
        'waiting' => 'Waiting',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'canceled' => 'Canceled',
    ],
    
    'statusClass' => [
        'new' => 'primary',
        'in_progress' => 'warning',
        'waiting' => 'info', 
        'resolved' => 'success',
        'closed' => 'secondary',
        'canceled' => 'danger',
    ],
    
    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High', 
        'urgent' => 'Urgent',
    ],
];