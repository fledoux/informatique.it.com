<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected $fillable = ['status','priority','company_id','author_id','assigned_to','assigned_at','due','folder_code','subject','question','billable'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime','status' => 'string','priority' => 'string','assigned_at' => 'datetime','billable' => 'boolean'];

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
     * Récupère les tickets récents avec leurs relations
     */
    public static function recent(int $limit = 20)
    {
        return self::with(['company', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcule toutes les statistiques du dashboard en une seule requête
     */
    public static function dashboardStats()
    {
        $now = now();
        
        $stats = self::selectRaw('
            COUNT(*) as tickets_count,
            COUNT(CASE WHEN status IN (?, ?, ?) THEN 1 END) as open_tickets_count,
            COUNT(CASE WHEN status = ? THEN 1 END) as waiting_count,
            COUNT(CASE WHEN due < ? AND status NOT IN (?, ?, ?) THEN 1 END) as overdue_count
        ', [
            TicketStatus::NEW->value,
            TicketStatus::IN_PROGRESS->value, 
            TicketStatus::WAITING->value,
            TicketStatus::WAITING->value,
            $now,
            TicketStatus::RESOLVED->value,
            TicketStatus::CLOSED->value,
            TicketStatus::CANCELED->value
        ])->first();

        return [
            'tickets_count' => $stats->tickets_count ?? 0,
            'open_tickets_count' => $stats->open_tickets_count ?? 0,
            'waiting_count' => $stats->waiting_count ?? 0,
            'overdue_count' => $stats->overdue_count ?? 0,
        ];
    }
}