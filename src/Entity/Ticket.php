<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $ticket_status = null;

    #[ORM\Column(length: 20)]
    private ?string $priority = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company_id = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $created_by_id = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $assigned_to_id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $assigned_at = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $code_folder = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $question = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['default' => null])]
    private ?\DateTimeImmutable $due_at = null;

    #[ORM\Column]
    private ?bool $is_billable = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketStatus(): ?string
    {
        return $this->ticket_status;
    }

    public function setTicketStatus(string $ticket_status): static
    {
        $this->ticket_status = $ticket_status;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCompanyId(): ?Company
    {
        return $this->company_id;
    }

    public function setCompanyId(?Company $company_id): static
    {
        $this->company_id = $company_id;

        return $this;
    }

    public function getCreatedById(): ?User
    {
        return $this->created_by_id;
    }

    public function setCreatedById(?User $created_by_id): static
    {
        $this->created_by_id = $created_by_id;

        return $this;
    }

    public function getAssignedToId(): ?User
    {
        return $this->assigned_to_id;
    }

    public function setAssignedToId(?User $assigned_to_id): static
    {
        $this->assigned_to_id = $assigned_to_id;
        if ($assigned_to_id !== null && $this->assigned_at === null) {
            $this->assigned_at = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(?\DateTimeImmutable $assigned_at): static
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getCodeFolder(): ?string
    {
        return $this->code_folder;
    }

    public function setCodeFolder(?string $code_folder): static
    {
        $this->code_folder = $code_folder;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getDueAt(): ?\DateTimeImmutable
    {
        return $this->due_at;
    }

    public function setDueAt(?\DateTimeImmutable $due_at): static
    {
        $this->due_at = $due_at;
        return $this;
    }

    public function isBillable(): ?bool
    {
        return $this->is_billable;
    }

    public function setIsBillable(bool $is_billable): static
    {
        $this->is_billable = $is_billable;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        if ($this->created_at === null) {
            $this->created_at = $now;
        }
        $this->updated_at = $now;
        if ($this->assigned_to_id !== null && $this->assigned_at === null) {
            $this->assigned_at = $now;
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        if ($this->assigned_to_id !== null && $this->assigned_at === null) {
            $this->assigned_at = new \DateTimeImmutable();
        }
    }
}
