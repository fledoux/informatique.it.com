<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    protected $fillable = ['status','company_id','ticket_id','message_id','uploaded_by','s3_path','original_filename','mime_type','size_bytes'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime'];

    /**
     * Relation : Société
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation : Ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relation : Message (optionnel)
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'message_id');
    }

    /**
     * Relation : Utilisateur qui a uploadé
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Créer une instance du client S3
     */
    protected static function getS3Client(): \Aws\S3\S3Client
    {
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);
    }

    /**
     * Générer une URL signée temporaire pour téléchargement sécurisé
     */
    public function getTemporaryUrl(int $minutes = 5): string
    {
        $s3Client = self::getS3Client();

        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => config('services.aws.bucket'),
            'Key' => $this->s3_path,
        ]);

        $request = $s3Client->createPresignedRequest($cmd, "+{$minutes} minutes");

        return (string) $request->getUri();
    }

    /**
     * Vérifier si le fichier existe sur S3
     */
    public function existsOnS3(): bool
    {
        try {
            $s3Client = self::getS3Client();
            return $s3Client->doesObjectExist(config('services.aws.bucket'), $this->s3_path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Supprimer le fichier de S3 et l'enregistrement
     */
    public function deleteWithFile(): bool
    {
        // Supprimer de S3
        if ($this->existsOnS3()) {
            try {
                $s3Client = self::getS3Client();
                $s3Client->deleteObject([
                    'Bucket' => config('services.aws.bucket'),
                    'Key' => $this->s3_path,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error deleting file from S3: {$e->getMessage()}");
            }
        }

        // Supprimer l'enregistrement
        return $this->delete();
    }

    /**
     * Formater la taille en format lisible
     */
    public function getFormattedSize(): string
    {
        if (!$this->size_bytes) {
            return 'N/A';
        }

        $units = ['o', 'Ko', 'Mo', 'Go'];
        $bytes = $this->size_bytes;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope : Fichiers actifs uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope : Fichiers d'un ticket spécifique
     */
    public function scopeForTicket($query, int $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    /**
     * Scope : Fichiers d'un message spécifique
     */
    public function scopeForMessage($query, int $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    /**
     * Déplace le fichier vers un nouveau ticket sur S3
     * 
     * @param int $newTicketId L'ID du nouveau ticket
     * @return bool Succès ou échec du déplacement
     */
    public function moveToTicket(int $newTicketId): bool
    {
        // Extraire le nom du fichier du chemin actuel
        // Exemple: tickets/5/abc123_file.pdf -> abc123_file.pdf
        $filename = basename($this->s3_path);

        // Construire le nouveau chemin S3
        $newPath = "tickets/{$newTicketId}/{$filename}";

        try {
            $s3Client = self::getS3Client();
            $bucket = config('services.aws.bucket');

            // Vérifier que le fichier source existe
            $exists = $s3Client->doesObjectExist($bucket, $this->s3_path);
            if (!$exists) {
                \Illuminate\Support\Facades\Log::warning("File not found on S3: {$this->s3_path}");
                return false;
            }

            // Copier le fichier vers le nouveau chemin
            $s3Client->copyObject([
                'Bucket' => $bucket,
                'Key' => $newPath,
                'CopySource' => "{$bucket}/{$this->s3_path}",
            ]);

            // Vérifier que la copie a réussi
            $copyExists = $s3Client->doesObjectExist($bucket, $newPath);
            if (!$copyExists) {
                \Illuminate\Support\Facades\Log::error("Failed to copy file to: {$newPath}");
                return false;
            }

            // Supprimer l'ancien fichier
            $s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $this->s3_path,
            ]);

            // Mettre à jour le chemin dans la base de données
            $this->s3_path = $newPath;
            $this->ticket_id = $newTicketId;
            $this->save();

            return true;

        } catch (\Aws\Exception\AwsException $e) {
            \Illuminate\Support\Facades\Log::error("AWS S3 error moving file: {$e->getMessage()}");
            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error moving file on S3: {$e->getMessage()}");
            return false;
        }
    }
}
