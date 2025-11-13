<?php

namespace App\Tests\Form;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    /**
     * Test valide : Vérifie qu'un commentaire valide passe la validation.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées pour un commentaire valide
        $formData = [
            'text' => 'Ceci est un commentaire valide qui contient plus de cinq caractères.',
        ];

        // Instancie une entité `Comment` vide pour comparaison
        $comment = new Comment();

        // Crée le formulaire
        $form = $this->factory->create(CommentType::class, $comment);

        // Soumet des données simulées au formulaire
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que les données soumises sont bien écrites dans l'entité
        $this->assertEquals($formData['text'], $comment->getText());

        // Vérifie que le formulaire est valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test invalide : Teste un commentaire vide.
     */
    public function testSubmitEmptyComment(): void
    {
        // Données simulées : commentaire vide
        $formData = [
            'text' => '',
        ];

        // Instancie une entité `Comment`
        $comment = new Comment();

        // Crée le formulaire
        $form = $this->factory->create(CommentType::class, $comment);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'EST PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie que le formulaire retourne un message d'erreur attendu
        $errors = $form->get('text')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('Le commentaire ne peut pas être vide.', $errors[0]->getMessage());
    }

    /**
     * Test invalide : Teste un commentaire trop court.
     */
    public function testSubmitTooShortComment(): void
    {
        // Données simulées : commentaire trop court
        $formData = [
            'text' => 'abc', // Moins que 5 caractères
        ];

        $comment = new Comment();

        // Crée le formulaire
        $form = $this->factory->create(CommentType::class, $comment);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'EST PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie que le formulaire retourne un message d'erreur pour la longueur minimale
        $errors = $form->get('text')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Le commentaire doit comporter au moins 5 caractères.', $errors[0]->getMessage());
    }

    /**
     * Test invalide : Teste un commentaire trop long.
     */
    public function testSubmitTooLongComment(): void
    {
        // Données simulées : texte de plus de 2000 caractères
        $longText = str_repeat('a', 2001); // Chaîne de test de 2001 caractères
        $formData = [
            'text' => $longText,
        ];

        $comment = new Comment();

        // Crée le formulaire
        $form = $this->factory->create(CommentType::class, $comment);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'EST PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie que le formulaire retourne un message d'erreur indiquant que le texte est trop long
        $errors = $form->get('text')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Le commentaire ne peut pas dépasser 2000 caractères.', $errors[0]->getMessage());
    }

    /**
     * Test des options de configuration du formulaire.
     */
    public function testConfigureOptions(): void
    {
        // Crée une instance du formulaire
        $form = $this->factory->create(CommentType::class);

        // Vérifie que l'option `data_class` est bien configurée
        $this->assertSame(Comment::class, $form->getConfig()->getOption('data_class'));
    }
}