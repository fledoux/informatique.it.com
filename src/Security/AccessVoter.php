<?php
// src/Security/Voter/AccessVoter.php
namespace App\Security;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AccessVoter extends Voter
{
    public const VIEW   = 'VIEW';
    public const EDIT   = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        // On est générique : on reconnaît EDIT/VIEW/DELETE
        // et on gère explicitement chaque type d’entité pris en charge ici.
        if (!\in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)) {
            return false;
        }

        // À ce stade, on supporte Ticket (tu pourras étendre à d'autres entités plus tard)
        return $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        // Super admin : accès total
        if (\in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Dispatch selon le type d’entité (ici Ticket)
        if ($subject instanceof Ticket) {
            return $this->voteTicket($attribute, $subject, $user);
        }

        return false;
    }

    private function voteTicket(string $attribute, Ticket $ticket, User $user): bool
    {
        return match ($attribute) {
            self::VIEW   => $this->canViewTicket($ticket, $user),
            self::EDIT   => $this->canEditTicket($ticket, $user),
            self::DELETE => $this->canEditTicket($ticket, $user), // même règle que EDIT
            default      => false,
        };
    }

    private function canViewTicket(Ticket $ticket, User $user): bool
    {
        // Créateur OU même société (si tu veux que VIEW soit plus permissif)
        if ($ticket->getCreatedById()?->getId() === $user->getId()) {
            return true;
        }
        $tCo = $ticket->getCompanyId();
        $uCo = method_exists($user, 'getCompany') ? $user->getCompany() : null;
        return $tCo && $uCo && $tCo->getId() === $uCo->getId();
    }

    private function canEditTicket(Ticket $ticket, User $user): bool
    {
        // Créateur
        if ($ticket->getCreatedById()?->getId() === $user->getId()) {
            return true;
        }

        // ROLE_ADMIN de la même société
        if (\in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $tCo = $ticket->getCompanyId();
            $uCo = method_exists($user, 'getCompany') ? $user->getCompany() : null;
            if ($tCo && $uCo && $tCo->getId() === $uCo->getId()) {
                return true;
            }
        }

        return false;
    }
}