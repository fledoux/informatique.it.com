<?php

return [
        'entity' => 'User',
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
            'password' => 'Password',
            'status' => 'Status',
            'company_id' => 'Company Id',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'initial' => 'Initial',
            'phone' => 'Phone',
            'last_login' => 'Last Login',
            'agree_terms' => 'Agree Terms',
            'channels' => 'Channels',
            'channels_email' => 'Channels Email',
            'channels_sms' => 'Channels Sms',
            'note' => 'Note',
            'roles' => 'Roles'
    ],

    'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive'
    ],
    'agree_terms' => [
            'oui' => 'Yes',
            'non' => 'No'
    ],

    'roles' => [
        'super-admin' => 'Super Administrator',
        'manager' => 'Manager',
        'user' => 'User'
    ]
];
