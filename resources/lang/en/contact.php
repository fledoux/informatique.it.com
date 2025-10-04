<?php

return [
        'entity' => 'Contact',
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
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'type' => 'Type',
            'need' => 'Need'
    ],

    'type' => [
        'particulier' => 'Individual',
        'entreprise' => 'Company',
        'association' => 'Association',
        'autre' => 'Other'
    ],

    'enum' => [
            'type' => [
                'active' => 'Active',
                'inactive' => 'Inactive'
            ]
    ]
];
