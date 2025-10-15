<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReplyCode extends Model
{
    protected $fillable = [
        'ticket_id',
        'code',
        'recipient_email',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relation avec le ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Scope pour les codes valides (non expirés)
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope pour les codes expirés
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
    }

    /**
     * Vérifie si le code est expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifie si le code est valide (non expiré)
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Marque le code comme utilisé
     */
    public function markAsUsed(): void
    {
        if (!$this->used_at) {
            $this->update(['used_at' => now()]);
        }
    }
}
