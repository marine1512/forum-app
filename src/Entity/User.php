<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité représentant un utilisateur.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['username'], message: 'Il existe déjà un compte avec cet identifiant.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * ID unique de l'utilisateur.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * Nom d'utilisateur unique.
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * Adresse e-mail unique de l'utilisateur.
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * Rôles associés à l'utilisateur (format JSON).
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * Mot de passe chiffré de l'utilisateur.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Indique si l'utilisateur est vérifié.
     */
    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    /**
     * Jeton pour la vérification de l'adresse e-mail.
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $emailVerificationToken = null;

    /**
     * Retourne l'ID de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur (adresse e-mail).
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Retourne le nom d'utilisateur.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur.
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Récupère l'adresse e-mail.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne les rôles associés à l'utilisateur.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe chiffré.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe.
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Efface les informations sensibles.
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Indique si l'utilisateur est vérifié.
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Définit si l'utilisateur est vérifié.
     */
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Retourne le jeton de vérification de l'e-mail.
     */
    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    /**
     * Définit le jeton de vérification de l'e-mail.
     */
    public function setEmailVerificationToken(?string $emailVerificationToken): self
    {
        $this->emailVerificationToken = $emailVerificationToken;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
        public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
