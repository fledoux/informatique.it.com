<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['status','subject','body','company_id','ticket_id','author_id'];

    /**
     * Boot du modèle - gère la suppression des pièces jointes
     */
    protected static function boot()
    {
        parent::boot();

        // Avant la suppression du message, supprimer toutes les pièces jointes
        static::deleting(function ($message) {
            // Récupérer toutes les pièces jointes (même inactives)
            $attachments = TicketAttachment::where('message_id', $message->id)->get();
            
            foreach ($attachments as $attachment) {
                // Supprimer le fichier de S3 via le helper
                if ($attachment->s3_path) {
                    \App\Helpers\Helper::destroyS3File($attachment->s3_path);
                }
                
                // Supprimer l'enregistrement en base
                $attachment->delete();
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime'];

    /**
     * Get the company that owns the ticket message.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the ticket that owns the message.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that authored the message.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the attachments for this message.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id')->where('status', 'active');
    }

    /**
     * Scope pour messages publics (actifs, visibles par le client)
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour messages internes uniquement
     */
    public function scopeInternal($query)
    {
        return $query->where('status', 'internal');
    }

    /**
     * Scope pour messages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour messages inactifs
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Vérifier si le message est interne
     */
    public function isInternal(): bool
    {
        return $this->status === 'internal';
    }

    /**
     * Vérifier si le message est public (visible client)
     */
    public function isPublic(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifier si le message est inactif
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
