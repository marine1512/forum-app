<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Comment;
use App\Entity\Sujet;
use App\Entity\User;

class CommentTest extends TestCase
{
    /**
     * Test des getters et setters de la classe Comment.
     */
    public function testCommentEntity(): void
    {
        // Initialisation de l'entité Comment
        $comment = new Comment();

        // Vérifie que l'id est null à l'initialisation
        $this->assertNull($comment->getId());

        // Test du text (contenu)
        $text = "Ceci est un commentaire de test.";
        $comment->setText($text);
        $this->assertEquals($text, $comment->getText());

        // Test de l'auteur (nom custom pour utilisateurs non enregistrés)
        $author = "Auteur Test";
        $comment->setAuthor($author);
        $this->assertEquals($author, $comment->getAuthor());

        // Test de la date
        $date = new \DateTimeImmutable();
        $comment->setDate($date);
        $this->assertEquals($date, $comment->getDate());

        // Test du sujet
        $sujet = new Sujet();
        $comment->setSubject($sujet);
        $this->assertSame($sujet, $comment->getSubject());

        // Test de l'utilisateur (auteur enregistré)
        $user = new User();
        $comment->setAuthorUser($user);
        $this->assertSame($user, $comment->getAuthorUser());
    }

    /**
     * Test des relations entre Comment et Sujet.
     */
    public function testCommentSujetRelation(): void
    {
        $comment = new Comment();
        $sujet = new Sujet();

        // Associer le sujet au commentaire
        $comment->setSubject($sujet);

        // Vérifie que le sujet associé est le bon
        $this->assertSame($sujet, $comment->getSubject());
    }

    /**
     * Test des relations entre Comment et User.
     */
    public function testCommentUserRelation(): void
    {
        $comment = new Comment();
        $user = new User();

        // Associer l'utilisateur au commentaire
        $comment->setAuthorUser($user);

        // Vérifie que l'utilisateur associé est le bon
        $this->assertSame($user, $comment->getAuthorUser());
    }
}