<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'billingContactId')]
    private Collection $companies;

    #[ORM\Column(length: 20, name: 'user_status')]
    private ?string $user_status = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'company_id', nullable: true)]
    private ?Company $company = null;

    #[ORM\Column(length: 120, name: 'first_name')]
    private ?string $first_name = null;

    #[ORM\Column(length: 120, name: 'last_name')]
    private ?string $last_name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true, name: 'last_login_at')]
    private ?\DateTimeImmutable $last_login_at = null;

    #[ORM\Column(name: 'agree_terms')]
    private ?bool $agree_terms = null;

    #[ORM\Column(nullable: true)]
    private ?array $channels = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(name: 'updated_at')]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $now = new \DateTimeImmutable();
        if ($this->user_status === null) {
            $this->user_status = 'active';
        }
        if ($this->agree_terms === null) {
            $this->agree_terms = false;
        }
        if ($this->created_at === null) {
            $this->created_at = $now;
        }
        if ($this->updated_at === null) {
            $this->updated_at = $now;
        }
        if ($this->first_name === null) {
            $this->first_name = '';
        }
        if ($this->last_name === null) {
            $this->last_name = '';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', (string) $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = strtoupper($role);
        $this->roles = array_values(array_unique($this->roles));
        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_values(array_diff($this->roles, [strtoupper($role)]));
        return $this;
    }

    /** Récupère les rôles "persistés" (sans ajout automatique de ROLE_USER) */
    public function getRolesRaw(): array
    {
        return $this->roles;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): static
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setBillingContactId($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): static
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getBillingContactId() === $this) {
                $company->setBillingContactId(null);
            }
        }

        return $this;
    }

    public function getUserStatus(): ?string
    {
        return $this->user_status;
    }

    public function setUserStatus(string $userStatus): static
    {
        $this->user_status = $userStatus;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @deprecated Use getCompany() instead
     */
    public function getCompanyId(): ?Company
    {
        return $this->getCompany();
    }

    /**
     * @deprecated Use setCompany() instead
     */
    public function setCompanyId(?Company $companyId): static
    {
        return $this->setCompany($companyId);
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $firstName): static
    {
        $this->first_name = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $lastName): static
    {
        $this->last_name = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->last_login_at;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): static
    {
        $this->last_login_at = $lastLoginAt;

        return $this;
    }

    public function isAgreeTerms(): ?bool
    {
        return $this->agree_terms;
    }

    public function setAgreeTerms(bool $agreeTerms): static
    {
        $this->agree_terms = $agreeTerms;

        return $this;
    }

    public function getChannels(): ?array
    {
        return $this->channels;
    }

    public function setChannels(?array $channels): static
    {
        $this->channels = $channels;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->created_at = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updated_at = $updatedAt;

        return $this;
    }
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->user_status === null) {
            $this->user_status = 'active';
        }
        if ($this->agree_terms === null) {
            $this->agree_terms = false;
        }
        $now = new \DateTimeImmutable();
        if ($this->created_at === null) {
            $this->created_at = $now;
        }
        if ($this->updated_at === null) {
            $this->updated_at = $now;
        }
        if ($this->first_name === null) {
            $this->first_name = '';
        }
        if ($this->last_name === null) {
            $this->last_name = '';
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }
}
