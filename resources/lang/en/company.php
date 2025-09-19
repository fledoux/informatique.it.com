<?php

return [
        'entity' => 'Company',
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
            'status' => 'Status',
            'name' => 'Name',
            'siret' => 'Siret',
            'vat_number' => 'Vat Number',
            'email' => 'Email',
            'phone' => 'Phone',
            'website' => 'Website',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'zip' => 'Zip',
            'city' => 'City',
            'country' => 'Country',
            'notes' => 'Notes'
    ],

            'enum.status.active' => 'Active',
            'enum.status.inactive' => 'Inactive'
];
