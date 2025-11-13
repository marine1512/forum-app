<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    /**
     * Test valide : vérifie que le formulaire accepte des données valides.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées valides
        $formData = [
            'username' => 'JohnDoe',
            'email'    => 'johndoe@example.com',
            'password' => 'SecurePassword123!',
        ];

        // Crée une instance vide de User pour la comparaison
        $user = new User();

        // Crée le formulaire
        $form = $this->factory->create(UserType::class, $user);

        // Soumet les données simulées
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que les champs mappés sont correctement remplis
        $this->assertEquals('JohnDoe', $user->getUsername());
        $this->assertEquals('johndoe@example.com', $user->getEmail());

        // Vérifie que le champ non mappé ne modifie pas directement l'objet
        $this->assertNull($user->getPassword()); // La propriété `password` est non mappée.

        // Vérifie que le formulaire est valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test invalide : vérifie que le champ `username` ne peut pas être vide.
     */
    public function testSubmitInvalidDataWithoutUsername(): void
    {
        // Données avec username vide
        $formData = [
            'username' => '',
            'email'    => 'johndoe@example.com',
            'password' => 'SecurePassword123!',
        ];

        $form = $this->factory->create(UserType::class, new User());

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Le formulaire doit être invalide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs sur le champ `username`
        $errors = $form->get('username')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Merci de saisir un pseudo.', $errors[0]->getMessage());
    }

    /**
     * Test invalide : vérifie que le champ `username` est trop court.
     */
    public function testSubmitUsernameTooShort(): void
    {
        // Données avec username trop court
        $formData = [
            'username' => 'Jo',
            'email'    => 'johndoe@example.com',
            'password' => 'SecurePassword123!',
        ];

        $form = $this->factory->create(UserType::class, new User());

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Le formulaire doit être invalide à cause de la contrainte de longueur
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs sur le champ `username`
        $errors = $form->get('username')->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString(
            'Votre pseudo doit comporter au moins',
            $errors[0]->getMessage()
        );
    }

    /**
     * Test invalide : vérifie que le champ `email` ne peut pas être vide.
     */
    public function testSubmitInvalidDataWithoutEmail(): void
    {
        // Données avec email vide
        $formData = [
            'username' => 'JohnDoe',
            'email'    => '',
            'password' => 'SecurePassword123!',
        ];

        $form = $this->factory->create(UserType::class, new User());

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Le formulaire doit être invalide
        $this->assertFalse($form->isValid());

        // Vérifie les erreurs sur le champ `email`
        $errors = $form->get('email')->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertEquals('Merci de saisir une adresse email.', $errors[0]->getMessage());
    }

    /**
     * Test pour les options de configuration.
     */
    public function testConfigureOptions(): void
    {
        $form = $this->factory->create(UserType::class);

        // Vérifie que l'option `data_class` est bien configurée
        $this->assertEquals(User::class, $form->getConfig()->getOption('data_class'));
    }
}