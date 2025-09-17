<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function getDashboardStats(\DateTimeInterface $now, array $openStatuses, array $waitingStatuses, array $closedStatuses): array
    {
        // Normalize: accept enums (BackedEnum) or strings
        $normalize = static function(array $items): array {
            return array_map(static function($v) {
                if ($v instanceof \BackedEnum) {
                    return (string) $v->value;
                }
                return (string) $v;
            }, $items);
        };

        $open    = $normalize($openStatuses);
        $waiting = $normalize($waitingStatuses);
        $closed  = $normalize($closedStatuses);

        // Fallbacks si aucune liste n'est passée
        if (!$open) {
            return [
                'total' => 0,
                'openCnt' => 0,
                'waitingCnt' => 0,
                'overdueCnt' => 0,
            ];
        }

        $qb = $this->createQueryBuilder('t');

        return $qb->select([
                'COUNT(t.id) AS total',
                "SUM(CASE WHEN t.ticket_status IN (:open) THEN 1 ELSE 0 END) AS openCnt",
                "SUM(CASE WHEN t.ticket_status IN (:waiting) THEN 1 ELSE 0 END) AS waitingCnt",
                "SUM(CASE WHEN t.due_at IS NOT NULL AND t.due_at < :now AND t.ticket_status NOT IN (:closed) THEN 1 ELSE 0 END) AS overdueCnt",
            ])
            ->setParameter('now', $now)
            ->setParameter('open', $open)
            ->setParameter('waiting', $waiting)
            ->setParameter('closed', $closed)
            ->getQuery()
            ->getSingleResult();
    }
}
