<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use App\Entity\Comment;

class UserTest extends TestCase
{
    /**
     * Teste les getters et setters simples.
     */
    public function testSimpleGettersAndSetters(): void
    {
        $user = new User();

        // Test du username
        $username = "TestUser";
        $user->setUsername($username);
        $this->assertEquals($username, $user->getUsername());

        // Test de l'email
        $email = "test@example.com";
        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());

        // Test des rôles
        $roles = ["ROLE_ADMIN", "ROLE_EDITOR"];
        $user->setRoles($roles);
        $this->assertEquals(array_unique([...$roles, "ROLE_USER"]), $user->getRoles());

        // Test du mot de passe
        $password = "hashedPassword123";
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());
    }

    /**
     * Teste l'initialisation des rôles avec ROLE_USER par défaut.
     */
    public function testRolesWithDefault(): void
    {
        $user = new User();

        // Test des rôles sans en avoir défini (doit inclure ROLE_USER par défaut)
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * Teste les méthodes liées aux statuts.
     */
    public function testStatusGettersAndSetters(): void
    {
        $user = new User();

        // Test de la vérification du compte utilisateur
        $user->setIsVerified(true);
        $this->assertTrue($user->isVerified());

        // Test du statut actif
        $user->setIsActive(true);
        $this->assertTrue($user->isActive());
    }

    /**
     * Teste l'initialisation automatique dans le constructeur.
     */
    public function testConstructorInitialization(): void
    {
        $user = new User();

        // Vérifie que la date de création est bien initialisée
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());

        // Vérifie que la collection des commentaires est initialisée
        $this->assertCount(0, $user->getComments());
    }

    /**
     * Teste les relations avec les commentaires.
     */
    public function testAddAndRemoveComments(): void
    {
        $user = new User();

        // Création de deux commentaires
        $comment1 = new Comment();
        $comment2 = new Comment();

        // Ajout des commentaires
        $user->addComment($comment1);
        $user->addComment($comment2);

        // Vérifie qu'ils sont bien ajoutés
        $this->assertCount(2, $user->getComments());
        $this->assertTrue($user->getComments()->contains($comment1));
        $this->assertTrue($user->getComments()->contains($comment2));

        // Vérifie que les commentaires ont cet utilisateur comme auteur
        $this->assertSame($user, $comment1->getAuthorUser());
        $this->assertSame($user, $comment2->getAuthorUser());

        // Supprime un commentaire
        $user->removeComment($comment1);

        // Vérifie que le commentaire est bien supprimé
        $this->assertCount(1, $user->getComments());
        $this->assertFalse($user->getComments()->contains($comment1));
        $this->assertNull($comment1->getAuthorUser());
    }

    /**
     * Teste que l'identifiant principal est bien renvoyé par `getUserIdentifier`.
     */
    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $username = "UniqueUsername";
        $user->setUsername($username);

        $this->assertEquals($username, $user->getUserIdentifier());
    }

    /**
     * Teste la méthode eraseCredentials (actuellement vide, mais doit être callable).
     */
    public function testEraseCredentials(): void
    {
        $user = new User();

        // Appelle la méthode (actuellement vide).
        $user->eraseCredentials();

        // Vérifie simplement qu'elle peut être appelée (aucune exception).
        $this->assertTrue(method_exists($user, 'eraseCredentials'));
    }
}