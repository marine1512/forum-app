<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Sujet;
use App\Entity\Category;
use App\Entity\Comment;

class SujetTest extends TestCase
{
    /**
     * Teste les getters et setters simples de la classe Sujet.
     */
    public function testSimpleGettersAndSetters(): void
    {
        $sujet = new Sujet();

        // Test du setter et getter pour le name
        $name = "Test Sujet";
        $sujet->setName($name);
        $this->assertEquals($name, $sujet->getName());

        // Test de la date de création
        $createdAt = new \DateTimeImmutable("2023-01-01 12:00:00");
        $sujet->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $sujet->getCreatedAt());

        // Test de la catégorie associée
        $category = new Category();
        $sujet->setCategory($category);
        $this->assertSame($category, $sujet->getCategory());
    }

    /**
     * Teste l'initialisation automatique du constructeur.
     */
    public function testConstructorInitialization(): void
    {
        $sujet = new Sujet();

        // Test que la liste des commentaires est bien initialisée.
        $this->assertCount(0, $sujet->getComments());

        // Vérifie si createdAt a été défini automatiquement
        $this->assertInstanceOf(\DateTimeInterface::class, $sujet->getCreatedAt());
    }

    /**
     * Teste l'ajout et la récupération des commentaires associés au sujet.
     */
    public function testAddAndGetComments(): void
    {
        $sujet = new Sujet();

        // Créer quelques commentaires
        $comment1 = new Comment();
        $comment2 = new Comment();

        // Ajouter les commentaires au sujet
        $sujet->addComment($comment1);
        $sujet->addComment($comment2);

        // Vérifie que les commentaires sont bien ajoutés
        $this->assertCount(2, $sujet->getComments());
        $this->assertTrue($sujet->getComments()->contains($comment1));
        $this->assertTrue($sujet->getComments()->contains($comment2));

        // Vérifie que le sujet est bien assigné aux commentaires
        $this->assertSame($sujet, $comment1->getSubject());
        $this->assertSame($sujet, $comment2->getSubject());
    }

    /**
     * Teste la suppression d'un commentaire associé au sujet.
     */
    public function testRemoveComment(): void
    {
        $sujet = new Sujet();

        // Créer un commentaire et l'ajouter
        $comment = new Comment();
        $sujet->addComment($comment);

        $this->assertCount(1, $sujet->getComments());

        // Supprimer le commentaire
        $sujet->removeComment($comment);

        // Vérifie que le commentaire est bien supprimé
        $this->assertCount(0, $sujet->getComments());
        $this->assertNull($comment->getSubject());
    }

    /**
     * Teste la callback onPrePersist pour l'initialisation de la date
     */
    public function testOnPrePersist(): void
    {
        $sujet = new Sujet();

        // Simule la persistance de l'entité
        $sujet->onPrePersist();

        // Vérifie si createdAt est défini
        $this->assertInstanceOf(\DateTimeInterface::class, $sujet->getCreatedAt());
    }
}