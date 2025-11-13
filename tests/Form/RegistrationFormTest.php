<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegistrationForm;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationFormTest extends TypeTestCase
{
    /**
     * Test valide : vérifie si le formulaire accepte des données correctes.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées valides
        $formData = [
            'username' => 'ValidUsername',
            'email' => 'valid@example.com',
            'plainPassword' => 'ValidPassword123',
        ];

        // Instancie une entité User vide pour la comparaison
        $user = new User();

        // Créer le formulaire
        $form = $this->factory->create(RegistrationForm::class, $user);

        // Soumet les données simulées
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que les données soumises sont bien mappées à l'entité
        $this->assertEquals('ValidUsername', $user->getUsername());
        $this->assertEquals('valid@example.com', $user->getEmail());

        // Le champ 'plainPassword' est `mapped => false`, il ne modifie donc pas directement l'entité : on doit vérifier sa soumission
        $this->assertEquals('ValidPassword123', $form->get('plainPassword')->getData());

        // Vérifie que le formulaire est valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette des données incomplètes (ex. mot de passe vide).
     */
    public function testSubmitEmptyPassword(): void
    {
        // Données simulées avec un mot de passe vide
        $formData = [
            'username' => 'ValidUsername',
            'email' => 'valid@example.com',
            'plainPassword' => '', // Mot de passe vide
        ];

        $user = new User();
        $form = $this->factory->create(RegistrationForm::class, $user);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les messages d'erreur sur le champ `plainPassword`
        $errors = $form->get('plainPassword')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Please enter a password', $errors[0]->getMessage());
    }

    /**
     * Test invalide : vérifie que le formulaire rejette un mot de passe trop court.
     */
    public function testSubmitTooShortPassword(): void
    {
        // Données simulées avec un mot de passe trop court
        $formData = [
            'username' => 'ValidUsername',
            'email' => 'valid@example.com',
            'plainPassword' => 'abc', // Mot de passe de moins de 6 caractères
        ];

        $user = new User();
        $form = $this->factory->create(RegistrationForm::class, $user);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie les messages d'erreur sur le champ `plainPassword`
        $errors = $form->get('plainPassword')->getErrors();
        $this->assertCount(1, $errors);
        
        $this->assertStringContainsString(
            'Your password should be at least 6 characters',
            $errors[0]->getMessage()
        );
    }

    /**
     * Teste les options de configuration du formulaire.
     */
    public function testConfigureOptions(): void
    {
        // Crée une instance du formulaire
        $form = $this->factory->create(RegistrationForm::class);

        // Vérifie que l'option `data_class` est bien configurée sur User::class
        $this->assertEquals(User::class, $form->getConfig()->getOption('data_class'));
    }
}