<?php

return [
        'entity' => 'TicketAttachment',
        'id' => 'ID',
        'List' => 'Liste',
        'Edit' => 'Modifier',
        'Details' => 'Détails',
        'Actions' => 'Actions',
        'New' => 'Nouveau',
        'Save' => 'Enregistrer',
        'Back' => 'Retour',
        'Delete' => 'Supprimer',
        'Delete?' => 'Supprimer ?',
        'No data' => 'Aucune donnée',
        'Add Attachment' => 'Ajouter une pièce jointe',

        'fields' => [
                'status' => 'Statut',
                'company_id' => 'Société',
                'ticket_id' => 'Ticket',
                'message_id' => 'Message',
                'uploaded_by' => 'Uploadé par',
                's3_path' => 'Chemin S3',
                'original_filename' => 'Nom du fichier',
                'mime_type' => 'Type MIME',
                'size_bytes' => 'Taille',
                'files' => 'Fichiers'
        ],

        'btn' => [
                'Upload' => '<i class="fa-regular fa-cloud-arrow-up"></i> Téléverser',
        ],

        'errors' => [
                'file_too_large' => 'Le fichier est trop volumineux. Taille maximale : :max',
                'invalid_mime_type' => 'Type de fichier non autorisé : :mime',
                'blocked_extension' => 'Extension de fichier interdite : :ext',
        ]
];
