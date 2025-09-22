<?php

return [
        'entity' => 'User',
        'id' => 'ID',
        'List' => 'Create',
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
            'phone' => 'Phone',
            'last_login' => 'Last Login',
            'agree_terms' => 'Agree Terms',
            'channels' => 'Channels',
            'channels_email' => 'Email',
            'channels_sms' => 'Sms',
            'note' => 'Note'
    ],

            'enum.status.active' => 'Actif',
            'enum.status.inactive' => 'Inactif',
            'enum.agree_terms.oui' => 'Oui',
            'enum.agree_terms.non' => 'Non'
];
