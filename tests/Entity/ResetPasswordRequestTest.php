<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\ResetPasswordRequest;
use App\Entity\User;

class ResetPasswordRequestTest extends TestCase
{
    /**
     * Teste l'initialisation de l'entité via le constructeur.
     */
    public function testConstructorInitializesFields(): void
    {
        // Préparer des données pour initialiser une demande
        $user = new User();
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector = 'random_selector';
        $hashedToken = 'hashed_reset_token';

        // Instancier la demande de réinitialisation
        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        // Vérifie que l'utilisateur a été correctement assigné
        $this->assertSame($user, $resetRequest->getUser());

        // Vérifie que la date d'expiration correspond à la valeur spécifiée
        $this->assertEquals($expiresAt, $resetRequest->getExpiresAt());

        // Vérifie que le jeton haché est correctement assigné
        $this->assertEquals($hashedToken, $resetRequest->getHashedToken());
    }

    /**
     * Teste les getters simples (getId et getUser).
     */
    public function testGetters(): void
    {
        $user = new User();
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector = 'random_selector';
        $hashedToken = 'hashed_reset_token';

        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        // Vérifie que l'identifiant est `null` avant persistance (non généré par Doctrine ici)
        $this->assertNull($resetRequest->getId());

        // Vérifie que l'utilisateur assigné est bien récupéré
        $this->assertSame($user, $resetRequest->getUser());
    }

    /**
     * Teste les fonctionnalités du trait ResetPasswordRequestTrait.
     */
    public function testTokenTraitMethods(): void
    {
        $user = new User();
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector = 'random_selector';
        $hashedToken = 'hashed_reset_token';

        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        // Vérifie la méthode `isExpired` (le token ne doit pas être expiré)
        $this->assertFalse($resetRequest->isExpired());

        // Vérifie que le hashed token est bien récupéré
        $this->assertEquals($hashedToken, $resetRequest->getHashedToken());
    }

    /**
     * Teste la vérification d'expiration passée (jeton expiré).
     */
    public function testTokenExpiration(): void
    {
        $user = new User();
        $expiresAt = new \DateTimeImmutable('-1 hour'); // Jeton déjà expiré
        $selector = 'random_selector';
        $hashedToken = 'hashed_reset_token';

        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        // Vérifie que le jeton est bien marqué comme expiré
        $this->assertTrue($resetRequest->isExpired());
    }
}