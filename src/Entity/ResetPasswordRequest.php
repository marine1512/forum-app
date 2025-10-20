<?php

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * Entité représentant une demande de réinitialisation de mot de passe.
 * 
 * Cette entité stocke les données nécessaires pour gérer le processus de
 * réinitialisation de mot de passe d’un utilisateur.
 */
#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    /**
     * Fournit des fonctionnalités aux entités de réinitialisation de mot de passe.
     * 
     * Ces fonctionnalités permettent de gérer les données relatives au processus
     * de réinitialisation comme le jeton de réinitialisation, sa date d'expiration, etc.
     */
    use ResetPasswordRequestTrait;

    /**
     * Identifiant unique de la demande.
     * 
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Utilisateur associé à la demande de réinitialisation de mot de passe.
     * 
     * @var User|null
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Constructeur de la classe `ResetPasswordRequest`.
     * 
     * @param User $user L'utilisateur ayant initié la demande.
     * @param \DateTimeInterface $expiresAt Date et heure d'expiration du jeton.
     * @param string $selector Identificateur unique pour la demande.
     * @param string $hashedToken Jeton de réinitialisation haché.
     */
    public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    /**
     * Récupère l'identifiant unique de la demande.
     * 
     * @return int|null L'identifiant de la demande.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'utilisateur associé à la demande.
     * 
     * @return User L'utilisateur ayant initié la demande de réinitialisation.
     */
    public function getUser(): User
    {
        return $this->user;
    }
}