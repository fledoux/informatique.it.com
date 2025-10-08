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
     * Générer une URL signée temporaire pour téléchargement sécurisé
     */
    public function getTemporaryUrl(int $minutes = 5): string
    {
        return Storage::disk('s3')->temporaryUrl($this->s3_path, now()->addMinutes($minutes));
    }

    /**
     * Vérifier si le fichier existe sur S3
     */
    public function existsOnS3(): bool
    {
        return Storage::disk('s3')->exists($this->s3_path);
    }

    /**
     * Supprimer le fichier de S3 et l'enregistrement
     */
    public function deleteWithFile(): bool
    {
        // Supprimer de S3
        if ($this->existsOnS3()) {
            Storage::disk('s3')->delete($this->s3_path);
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
}
