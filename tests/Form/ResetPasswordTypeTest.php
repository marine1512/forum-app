<?php

namespace App\Tests\Form;

use App\Form\ResetPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class ResetPasswordTypeTest extends TypeTestCase
{
    /**
     * Test valide : vérifie que le formulaire accepte des données valides.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées valides
        $formData = [
            'newPassword' => 'SecurePassword123!',
            'confirmPassword' => 'SecurePassword123!',
        ];

        // Crée le formulaire
        $form = $this->factory->create(ResetPasswordType::class);

        // Soumet les données simulées
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire est valide
        $this->assertTrue($form->isValid());

        // Vérifie les données transmises au formulaire
        $this->assertEquals('SecurePassword123!', $form->get('newPassword')->getData());
        $this->assertEquals('SecurePassword123!', $form->get('confirmPassword')->getData());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un mot de passe vide.
     */
    public function testSubmitEmptyPassword(): void
    {
        // Données simulées avec un mot de passe vide
        $formData = [
            'newPassword' => '',
            'confirmPassword' => '',
        ];

        // Crée le formulaire
        $form = $this->factory->create(ResetPasswordType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs sur le champ `newPassword`
        $errors = $form->get('newPassword')->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertEquals('Veuillez entrer un mot de passe.', $errors[0]->getMessage());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un mot de passe trop court.
     */
    public function testSubmitPasswordTooShort(): void
    {
        // Données simulées avec un mot de passe de moins de 8 caractères
        $formData = [
            'newPassword' => 'abc123',
            'confirmPassword' => 'abc123',
        ];

        $form = $this->factory->create(ResetPasswordType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs de validation du champ `newPassword`
        $errors = $form->get('newPassword')->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString(
            'Votre mot de passe doit comporter au moins 8 caractères.',
            $errors[0]->getMessage()
        );
    }

    /**
     * Test invalide : vérifie que les mots de passe ne correspondant pas sont rejetés.
     */
    public function testPasswordMismatch(): void
    {
        // Données simulées avec des mots de passe différents
        $formData = [
            'newPassword' => 'SecurePassword123!',
            'confirmPassword' => 'DifferentPassword!',
        ];

        $form = $this->factory->create(ResetPasswordType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'est PAS valide
        $this->assertFalse($form->isValid());

        // Aucun champ par défaut ne compare les deux mots de passe, il faudrait ici un validateur personnalisé.
        // On peut vérifier les données soumises mais pas validées.
        $this->assertEquals('SecurePassword123!', $form->get('newPassword')->getData());
        $this->assertEquals('DifferentPassword!', $form->get('confirmPassword')->getData());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un mot de passe compromis.
     */
    public function testSubmitCompromisedPassword(): void
    {
        // Données simulées avec un mot de passe compromis
        $formData = [
            'newPassword' => '12345678', // Ce mot de passe est probablement compromis
            'confirmPassword' => '12345678',
        ];

        $form = $this->factory->create(ResetPasswordType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs du champ `newPassword`
        $errors = $form->get('newPassword')->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Ce mot de passe a été compromis lors d\'une fuite de données. Veuillez utiliser un autre mot de passe.',
            $errors[0]->getMessage()
        );
    }
}