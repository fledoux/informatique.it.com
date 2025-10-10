<?php

namespace App\Services;

use App\Models\TicketAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Aws\S3\S3Client;

class AttachmentService
{
    private S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'eu-west-3'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * Valider un fichier uploadé
     *
     * @throws \Exception
     */
    public function validateFile(UploadedFile $file): void
    {
        $maxSize = config('attachments.max_size', 20971520);
        $extension = strtolower($file->getClientOriginalExtension());
        $blockedExtensions = config('attachments.blocked_extensions', []);

        // Vérifier l'extension
        if (in_array($extension, $blockedExtensions)) {
            throw new \Exception(__('ticketattachment.errors.blocked_extension', [
                'ext' => $extension
            ]));
        }

        // Vérifier la taille
        if ($file->getSize() > $maxSize) {
            throw new \Exception(__('ticketattachment.errors.file_too_large', [
                'max' => \App\Helpers\Helper::formatBytes($maxSize)
            ]));
        }
    }

    /**
     * MÉTHODE PRIVÉE CENTRALE : Upload S3 + enregistrement DB
     * Utilisée par uploadMultipleAttachments() et uploadEmailAttachments()
     * 
     * @param string $binaryData Contenu binaire du fichier
     * @param string $originalFilename Nom original du fichier
     * @param string $mimeType Type MIME
     * @param int $sizeBytes Taille en bytes
     */
    private function uploadToS3AndSave(
        string $binaryData,
        string $originalFilename,
        string $mimeType,
        int $sizeBytes,
        int $ticketId,
        int $companyId,
        int $uploadedBy,
        ?int $messageId = null
    ): TicketAttachment {
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

        // Générer un nom unique
        $randomName = Str::random(32);
        $storedFilename = $randomName . '.' . $extension;
        $s3Key = "ticket/attachments/{$ticketId}/{$storedFilename}";

        Log::info("Uploading to S3: bucket=" . env('AWS_BUCKET') . ", key={$s3Key}, size={$sizeBytes}");

        // Uploader vers S3
        $this->s3Client->putObject([
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $s3Key,
            'Body' => $binaryData,
            'ContentType' => $mimeType,
            'ACL' => 'private',
        ]);

        Log::info("✅ S3 upload successful: {$originalFilename} -> {$s3Key}");

        // Créer l'enregistrement en base
        $attachmentModel = TicketAttachment::create([
            'status' => 'active',
            'company_id' => $companyId,
            'ticket_id' => $ticketId,
            'message_id' => $messageId,
            'uploaded_by' => $uploadedBy,
            's3_path' => $s3Key,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
        ]);

        Log::info("✅ Attachment saved to DB: ID={$attachmentModel->id}");

        return $attachmentModel;
    }

    /**
     * Uploader un fichier UploadedFile vers S3
     */
    public function uploadAttachment(
        UploadedFile $file,
        int $ticketId,
        int $companyId,
        int $uploadedBy,
        ?int $messageId = null
    ): TicketAttachment {
        Log::info("Traitement du fichier: {$file->getClientOriginalName()}");

        // Valider le fichier
        $this->validateFile($file);

        // Appeler la méthode centralisée
        return $this->uploadToS3AndSave(
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName(),
            $file->getMimeType(),
            $file->getSize(),
            $ticketId,
            $companyId,
            $uploadedBy,
            $messageId
        );
    }

    /**
     * Uploader plusieurs fichiers depuis UploadedFile[]
     *
     * @param array $files Tableau de UploadedFile
     * @return array Tableau de TicketAttachment créés
     */
    public function uploadMultipleAttachments(
        array $files,
        int $ticketId,
        int $companyId,
        int $uploadedBy,
        ?int $messageId = null
    ): array {
        Log::info("Traitement de " . count($files) . " fichier(s) pour le Support n°{$ticketId}" . ($messageId ? ", message #{$messageId}" : ""));

        $attachments = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                try {
                    $attachments[] = $this->uploadAttachment(
                        $file,
                        $ticketId,
                        $companyId,
                        $uploadedBy,
                        $messageId
                    );
                } catch (\Aws\S3\Exception\S3Exception $e) {
                    Log::error("❌ S3 error for {$file->getClientOriginalName()}: " . $e->getAwsErrorMessage());
                } catch (\Exception $e) {
                    Log::error("❌ Failed to upload attachment {$file->getClientOriginalName()}: " . $e->getMessage());
                }
            }
        }

        if (count($attachments) > 0) {
            Log::info("✅ Total de " . count($attachments) . " fichier(s) uploadé(s)");
        }

        return $attachments;
    }

    /**
     * Uploader plusieurs fichiers depuis un email IMAP (format différent)
     *
     * @param array $attachments Tableau avec ['data', 'filename', 'mime_type', 'size']
     * @return array Tableau de TicketAttachment créés
     */
    public function uploadEmailAttachments(
        array $attachments,
        int $ticketId,
        int $companyId,
        int $uploadedBy,
        ?int $messageId = null
    ): array {
        Log::info("Traitement de " . count($attachments) . " pièce(s) jointe(s) d'email pour le Support n°{$ticketId}");

        $savedAttachments = [];

        foreach ($attachments as $attachment) {
            try {
                // Validation de la taille
                $maxSize = config('attachments.max_size', 20971520);
                if ($attachment['size'] > $maxSize) {
                    Log::warning("❌ Attachment too large, skipping: {$attachment['filename']} ({$attachment['size']} bytes)");
                    continue;
                }

                // Validation de l'extension
                $extension = pathinfo($attachment['filename'], PATHINFO_EXTENSION);
                $blockedExtensions = config('attachments.blocked_extensions', []);
                if (in_array(strtolower($extension), $blockedExtensions)) {
                    Log::warning("❌ Blocked extension, skipping: {$attachment['filename']}");
                    continue;
                }

                // Appeler la méthode centralisée pour l'upload
                $savedAttachments[] = $this->uploadToS3AndSave(
                    $attachment['data'],
                    $attachment['filename'],
                    $attachment['mime_type'],
                    $attachment['size'],
                    $ticketId,
                    $companyId,
                    $uploadedBy,
                    $messageId
                );

            } catch (\Aws\S3\Exception\S3Exception $e) {
                Log::error("❌ S3 error for {$attachment['filename']}: " . $e->getAwsErrorMessage());
            } catch (\Exception $e) {
                Log::error("❌ Failed to save attachment {$attachment['filename']}: " . $e->getMessage());
            }
        }

        if (count($savedAttachments) > 0) {
            Log::info("✅ Total de " . count($savedAttachments) . " pièce(s) jointe(s) uploadée(s) depuis email");
        }

        return $savedAttachments;
    }
}
