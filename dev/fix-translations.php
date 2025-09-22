#!/usr/bin/env php
<?php

/**
 * Script de correction automatique des traductions
 */

// Commandes de base à traduire
$basicTranslations = [
    'fr' => [
        'entity' => 'entity', // Sera remplacé par le nom français de l'entité
        'List' => 'Liste',
        'YourList' => 'Votre liste',
        'Edit' => 'Modifier',
        'Details' => 'Détails',
        'Actions' => 'Actions',
        'New' => 'Nouveau',
        'Save' => 'Enregistrer',
        'Back' => 'Retour',
        'Delete' => 'Supprimer',
        'Delete?' => 'Supprimer ?',
        'No data' => 'Aucune donnée',
    ],
    'en' => [
        'entity' => 'entity', // Sera remplacé par le nom anglais de l'entité
        'List' => 'List',
        'YourList' => 'Your list',
        'Edit' => 'Edit',
        'Details' => 'Details',
        'Actions' => 'Actions',
        'New' => 'New',
        'Save' => 'Save',
        'Back' => 'Back',
        'Delete' => 'Delete',
        'Delete?' => 'Delete?',
        'No data' => 'No data',
    ]
];

// Traductions de champs communs
$fieldTranslations = [
    'fr' => [
        'id' => 'ID',
        'name' => 'Nom',
        'email' => 'E-mail',
        'phone' => 'Téléphone',
        'status' => 'Statut',
        'created_at' => 'Créé le',
        'updated_at' => 'Modifié le',
        'company_id' => 'Société',
        'user_id' => 'Utilisateur',
        'author_id' => 'Auteur',
        'assigned_to' => 'Assigné à',
        'subject' => 'Sujet',
        'question' => 'Question',
        'notes' => 'Notes',
        'website' => 'Site web',
        'address_line1' => 'Adresse ligne 1',
        'address_line2' => 'Adresse ligne 2',
        'zip' => 'Code postal',
        'city' => 'Ville',
        'country' => 'Pays',
        'siret' => 'Numéro SIRET',
        'vat_number' => 'Numéro de TVA',
        'priority' => 'Priorité',
        'due' => 'Échéance',
        'billable' => 'Facturable',
        'type' => 'Type',
        'need' => 'Besoin',
        'password' => 'Mot de passe',
        'firstname' => 'Prénom',
        'lastname' => 'Nom de famille',
        'last_login' => 'Dernière connexion',
        'agree_terms' => 'J\'accepte les conditions',
        'channels' => 'Canaux de notification',
        'note' => 'Note',
        'folder_code' => 'Code dossier',
        'assigned_at' => 'Assigné le',
        'channels_email' => 'Notifications par e-mail',
        'channels_sms' => 'Notifications par SMS',
    ],
    'en' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'status' => 'Status',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'company_id' => 'Company',
        'user_id' => 'User',
        'author_id' => 'Author',
        'assigned_to' => 'Assigned to',
        'subject' => 'Subject',
        'question' => 'Question',
        'notes' => 'Notes',
        'website' => 'Website',
        'address_line1' => 'Address line 1',
        'address_line2' => 'Address line 2',
        'zip' => 'Postal code',
        'city' => 'City',
        'country' => 'Country',
        'siret' => 'SIRET number',
        'vat_number' => 'VAT number',
        'priority' => 'Priority',
        'due' => 'Due date',
        'billable' => 'Billable',
        'type' => 'Type',
        'need' => 'Need',
        'password' => 'Password',
        'firstname' => 'First name',
        'lastname' => 'Last name',
        'last_login' => 'Last login',
        'agree_terms' => 'I agree to the terms',
        'channels' => 'Notification channels',
        'note' => 'Note',
        'folder_code' => 'Folder code',
        'assigned_at' => 'Assigned at',
        'channels_email' => 'Email notifications',
        'channels_sms' => 'SMS notifications',
    ]
];

// Traductions d'énumérations
$enumTranslations = [
    'fr' => [
        'status' => [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'new' => 'Nouveau',
            'in_progress' => 'En cours',
            'waiting' => 'En attente', 
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
            'canceled' => 'Annulé',
        ],
        'priority' => [
            'low' => 'Basse',
            'normal' => 'Normale',
            'high' => 'Haute',
            'urgent' => 'Urgente',
        ],
        'agree_terms' => [
            'oui' => 'Oui',
            'non' => 'Non',
        ]
    ],
    'en' => [
        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'new' => 'New',
            'in_progress' => 'In progress',
            'waiting' => 'Waiting',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'canceled' => 'Canceled',
        ],
        'priority' => [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ],
        'agree_terms' => [
            'oui' => 'Yes',
            'non' => 'No',
        ]
    ]
];

// Noms d'entités
$entityNames = [
    'fr' => [
        'Company' => 'Société',
        'User' => 'Utilisateur',
        'Ticket' => 'Ticket',
        'Contact' => 'Contact',
    ],
    'en' => [
        'Company' => 'Company',
        'User' => 'User', 
        'Ticket' => 'Ticket',
        'Contact' => 'Contact',
    ]
];

echo "🔧 CORRECTION DES TRADUCTIONS\n";
echo "=============================\n\n";

$basePath = dirname(__DIR__); // Remonter au dossier parent (racine du projet)
$correctionsMade = 0;

foreach (['fr', 'en'] as $lang) {
    $langPath = $basePath . '/resources/lang/' . $lang;
    
    foreach (['Company', 'User', 'Ticket', 'Contact'] as $entity) {
        $entityFile = $langPath . '/' . strtolower($entity) . '.php';
        
        if (!file_exists($entityFile)) {
            echo "⏭️  Fichier manquant: $entityFile\n";
            continue;
        }
        
        echo "🔍 Vérification: " . basename($entityFile) . " ($lang)\n";
        
        $translations = include $entityFile;
        $modified = false;
        
        // Corriger les traductions de base
        foreach ($basicTranslations[$lang] as $key => $value) {
            if ($key === 'entity') {
                $value = $entityNames[$lang][$entity];
            } elseif ($key === 'List') {
                $value = ($lang === 'fr') ? 
                    'Liste des ' . strtolower($entityNames[$lang][$entity]) . 's' :
                    $entityNames[$lang][$entity] . ' list';
            } elseif ($key === 'YourList') {
                $value = ($lang === 'fr') ? 
                    'Vos ' . strtolower($entityNames[$lang][$entity]) . 's' :
                    'Your ' . strtolower($entityNames[$lang][$entity]) . 's';
            }
            
            if (!isset($translations[$key]) || empty($translations[$key])) {
                $translations[$key] = $value;
                $modified = true;
                echo "  ✅ Ajout: $key = '$value'\n";
            }
        }
        
        // Corriger les champs
        if (!isset($translations['fields']) || !is_array($translations['fields'])) {
            $translations['fields'] = [];
        }
        
        foreach ($fieldTranslations[$lang] as $fieldKey => $fieldValue) {
            if (!isset($translations['fields'][$fieldKey]) || empty($translations['fields'][$fieldKey])) {
                $translations['fields'][$fieldKey] = $fieldValue;
                $modified = true;
                echo "  ✅ Ajout field: $fieldKey = '$fieldValue'\n";
            }
        }
        
        if ($modified) {
            // Sauvegarder le fichier
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            file_put_contents($entityFile, $content);
            $correctionsMade++;
            echo "  💾 Fichier sauvegardé\n";
        }
        
        echo "\n";
    }
}

echo "✅ Corrections terminées: $correctionsMade fichier(s) modifié(s)\n";