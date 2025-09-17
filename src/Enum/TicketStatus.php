<?php

namespace App\Enum;

enum TicketStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Canceled = 'canceled';
}