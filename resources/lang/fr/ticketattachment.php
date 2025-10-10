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
        'h1' => [
                'List' => '<i class="fa-regular fa-file-attachment me-2"></i>Pièces jointes',
                'Create' => '<i class="fa-regular fa-file-import me-2"></i>Nouvelle pièce jointe',
                'Edit' => '<i class="fa-regular fa-pen-to-square me-2"></i>Modifier la pièce jointe',
                'Details' => '<i class="fa-regular fa-eye me-2"></i>Détails de la pièce jointe',
                'Add Attachment' => '<i class="fa-regular fa-file-import me-2"></i>Ajouter une pièce jointe'
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
