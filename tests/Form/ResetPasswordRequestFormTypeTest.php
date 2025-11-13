<?php

namespace App\Tests\Form;

use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class ResetPasswordRequestFormTypeTest extends TypeTestCase
{
    /**
     * Test valide : vérifie que le formulaire accepte un email valide.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées valides
        $formData = [
            'email' => 'user@example.com', // Email valide
        ];

        // Crée le formulaire
        $form = $this->factory->create(ResetPasswordRequestFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que la valeur soumise est correcte
        $this->assertEquals('user@example.com', $form->get('email')->getData());

        // Vérifie que le formulaire est valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un email vide.
     */
    public function testSubmitEmptyEmail(): void
    {
        // Données simulées avec un email vide
        $formData = [
            'email' => '', // Email vide
        ];

        // Crée le formulaire
        $form = $this->factory->create(ResetPasswordRequestFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs de validation du champ "email"
        $errors = $form->get('email')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Veuillez saisir votre adresse email.', $errors[0]->getMessage());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un format non valide pour l'email.
     */
    public function testSubmitInvalidEmailFormat(): void
    {
        // Données simulées avec un email non valide
        $formData = [
            'email' => 'invalid-email', // Mauvais format d'email
        ];

        // Crée le formulaire
        $form = $this->factory->create(ResetPasswordRequestFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire est synchrone mais non valide (le champ EmailType déclenche des erreurs)
        $this->assertFalse($form->isValid());
    }

    /**
     * Test des options de configuration du formulaire.
     */
    public function testConfigureOptions(): void
    {
        // Crée une instance du formulaire
        $form = $this->factory->create(ResetPasswordRequestFormType::class);

        // Vérifie que l'option `data_class` est null (car il n'est pas mappé à une entité)
        $this->assertNull($form->getConfig()->getOption('data_class'));
    }
}