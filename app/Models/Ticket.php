<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Services\PushoverService;

/**
 * Enum pour les statuts de ticket
 */
enum TicketStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case WAITING = 'waiting';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case CANCELED = 'canceled';
}

/**
 * Enum pour les priorités
 */
enum TicketPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';
}

class Ticket extends Model
{
    protected $fillable = ['status', 'priority', 'company_id', 'author_id', 'assigned_to', 'assigned_at', 'due', 'folder_code', 'subject', 'question', 'billable', 'source', 'email_message_id', 'public_uuid', 'public_uuid_expires'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'status' => 'string', 'priority' => 'string', 'assigned_at' => 'datetime', 'due' => 'datetime', 'billable' => 'boolean', 'public_uuid_expires' => 'datetime'];

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Supprimer tous les fichiers S3 du ticket avant suppression
        static::deleting(function ($ticket) {
            try {
                $attachments = $ticket->attachments;
                
                if ($attachments->count() > 0) {
                    foreach ($attachments as $attachment) {
                        // Supprimer le fichier de S3 via le helper
                        if ($attachment->s3_path) {
                            \App\Helpers\Helper::destroyS3File($attachment->s3_path);
                        }
                    }

                    // Supprimer les enregistrements en base (cascade delete devrait le faire aussi)
                    $ticket->attachments()->delete();
                    
                    \Illuminate\Support\Facades\Log::info("Deleted {$attachments->count()} attachment(s) for ticket #{$ticket->id}");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error deleting ticket attachments: " . $e->getMessage());
            }
        });
    }

    /**
     * Relation vers la société
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation vers l'auteur du ticket (client)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relation vers l'utilisateur assigné
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relation vers les messages du ticket
     */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    /**
     * Messages publics du ticket (visibles par le client)
     */
    public function publicMessages()
    {
        return $this->hasMany(TicketMessage::class)->where('status', 'active');
    }

    /**
     * Messages internes du ticket (visibles seulement par l'équipe)
     */
    public function internalMessages()
    {
        return $this->hasMany(TicketMessage::class)->where('status', 'internal');
    }

    /**
     * Messages actifs du ticket (publics uniquement)
     */
    public function activeMessages()
    {
        return $this->hasMany(TicketMessage::class)->where('status', 'active');
    }

    /**
     * Messages visibles selon le rôle de l'utilisateur
     */
    public function visibleMessages($user = null)
    {
        $user = $user ?? Auth::user();
        
        if ($user && $user->hasRole('super-admin')) {
            // Super-admin voit tous les messages
            return $this->hasMany(TicketMessage::class);
        } else {
            // Autres utilisateurs ne voient que les messages actifs
            return $this->hasMany(TicketMessage::class)->where('status', 'active');
        }
    }

    /**
     * Relation vers les pièces jointes du ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Pièces jointes actives uniquement
     */
    public function activeAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->where('status', 'active');
    }

    /**
     * Pièces jointes de la demande initiale (sans message_id)
     */
    public function initialAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)
            ->whereNull('message_id')
            ->where('status', 'active');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeWithStatus($query, TicketStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les tickets ouverts (non fermés)
     */
    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [
            TicketStatus::RESOLVED,
            TicketStatus::CLOSED,
            TicketStatus::CANCELED
        ]);
    }

    /**
     * Scope pour les tickets assignés à un utilisateur
     */
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope pour les tickets d'une société
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Récupère les managers de la société du ticket
     */
    public function getManagers()
    {
        if (!$this->company_id) {
            return collect();
        }
        
        return User::where('company_id', $this->company_id)
            ->role('manager')
            ->get();
    }

    /**
     * Récupère les tickets récents avec leurs relations
     */
    public static function getMyLastTickets(int $limit = 20)
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        $query = self::with(['company', 'author', 'assignedTo']);

        // Super-admin : tous les tickets
        if ($user->hasRole('super-admin')) {
            // Pas de restriction
        }
        // Admin et Manager : tous les tickets de leur société
        elseif ($user->hasRole(['admin', 'manager'])) {
            $query->where('company_id', $user->company_id);
        }
        // User : seulement ses propres tickets
        else {
            $query->where('author_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcule toutes les statistiques du dashboard selon les permissions de l'utilisateur connecté
     */
    public static function dashboardStats()
    {
        $user = Auth::user();
        $now = now();

        if (!$user) {
            return [
                'tickets_count' => 0,
                'open_tickets_count' => 0,
                'waiting_count' => 0,
                'overdue_count' => 0,
            ];
        }

        // Construire la requête de base selon le rôle
        $baseQuery = self::query();

        // Appliquer les filtres selon le rôle
        if ($user->hasRole('super-admin')) {
            // Super Admin voit tous les tickets - pas de filtre
        } elseif ($user->hasRole(['admin', 'manager'])) {
            // Admin et Manager voient seulement les tickets de leur société
            $baseQuery->where('company_id', $user->company_id);
        } else {
            // Utilisateur normal voit seulement ses tickets
            $baseQuery->where('author_id', $user->id);
        }

        // Calculer chaque statistique séparément
        $tickets_count = (clone $baseQuery)->count();
        
        $open_tickets_count = (clone $baseQuery)
            ->whereIn('status', [
                TicketStatus::NEW->value,
                TicketStatus::IN_PROGRESS->value,
                TicketStatus::WAITING->value
            ])
            ->count();
        
        $waiting_count = (clone $baseQuery)
            ->where('status', TicketStatus::WAITING->value)
            ->count();
        
        $overdue_count = (clone $baseQuery)
            ->whereNotNull('due')
            ->where('due', '<', $now)
            ->whereNotIn('status', [
                TicketStatus::RESOLVED->value,
                TicketStatus::CLOSED->value,
                TicketStatus::CANCELED->value
            ])
            ->count();

        return [
            'tickets_count' => $tickets_count,
            'open_tickets_count' => $open_tickets_count,
            'waiting_count' => $waiting_count,
            'overdue_count' => $overdue_count,
        ];
    }

    /**
     * Retourne un array des utilisateurs de la société 1 pour le select assigned_to
     */
    public static function getAssignedToOptions(): array
    {
        return \App\Models\User::where('company_id', 1)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Retourne un array des utilisateurs de la société de l'utilisateur connecté pour les selects
     */
    public static function selectAllByCompany(): array
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            return [];
        }

        return \App\Models\User::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Retourne les tickets selon les permissions de l'utilisateur connecté
     */
    public static function getAllForUser()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        if ($user->hasRole('super-admin')) {
            // Super Admin voit tous les tickets
            return self::with(['company', 'author', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->hasRole('manager')) {
            // Utilisateur normal voit seulement les tickets de sa société
            return self::with(['company', 'author', 'assignedTo'])
                ->where('company_id', $user->company_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Utilisateur normal voit seulement ses tickets
            return self::with(['company', 'author', 'assignedTo'])
                ->where('company_id', $user->company_id)
                ->where('author_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    /**
     * Génère ou récupère le lien public pour ce ticket
     */
    public function getPublicLink(): string
    {
        // Si pas d'UUID ou expiré, en créer un nouveau
        if (!$this->public_uuid || ($this->public_uuid_expires && $this->public_uuid_expires < now())) {
            $this->update([
                'public_uuid' => \Illuminate\Support\Str::uuid(),
                'public_uuid_expires' => now()->addDays(7)
            ]);
        }

        return route('ticket.public', $this->public_uuid);
    }

    /**
     * Envoie un SMS avec le lien public vers le ticket
     */
    public function sendSmsWithLink(string $phoneNumber, string $message): bool
    {
        $publicLink = $this->getPublicLink();
        $smsContent = "Ticket #{$this->id}: {$message}\n\nRépondre: {$publicLink}";
        
        return \App\Services\SmsService::send($phoneNumber, $smsContent);
    }

    /**
     * Trouve un ticket par son UUID public
     */
    public static function findByPublicUuid(string $uuid): ?self
    {
        return self::where('public_uuid', $uuid)
            ->where(function($query) {
                $query->whereNull('public_uuid_expires')
                    ->orWhere('public_uuid_expires', '>', now());
            })
            ->first();
    }
}
