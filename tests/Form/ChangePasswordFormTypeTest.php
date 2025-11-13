<?php

namespace App\Tests\Form;

use App\Form\ChangePasswordFormType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ChangePasswordFormTypeTest extends TypeTestCase
{
    /**
     * Teste si le formulaire est valide avec des données correctes.
     */
    public function testSubmitValidData(): void
    {
        // Données valides simulées
        $formData = [
            'plainPassword' => [
                'first' => 'ValidPassword123!',
                'second' => 'ValidPassword123!',
            ],
        ];

        // Crée le formulaire
        $form = $this->factory->create(ChangePasswordFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé et valide
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
    }

    /**
     * Teste si le formulaire rejette des données avec des champs de mot de passe non identiques.
     */
    public function testPasswordMismatch(): void
    {
        // Données invalides (mots de passe différents)
        $formData = [
            'plainPassword' => [
                'first' => 'ValidPassword123!',
                'second' => 'DifferentPassword456!',
            ],
        ];

        $form = $this->factory->create(ChangePasswordFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'EST PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie le message d'erreur attendu (champs non identiques)
        $errors = $form->getErrors(true);
        $this->assertCount(1, $errors);
        $this->assertEquals('Les deux champs de mot de passe doivent correspondre.', $errors->current()->getMessage());
    }

    /**
     * Teste si le formulaire rejette un mot de passe trop court.
     */
    public function testPasswordTooShort(): void
    {
        // Données invalides (mot de passe trop court)
        $formData = [
            'plainPassword' => [
                'first' => 'short',
                'second' => 'short',
            ],
        ];

        $form = $this->factory->create(ChangePasswordFormType::class);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire N'EST PAS valide
        $this->assertFalse($form->isValid());

        // Vérifie le message d'erreur attendu
        $errors = $form->get('plainPassword')->get('first')->getErrors();
        $this->assertGreaterThan(0, count($errors));

        // Vérifie que l'erreur concerne la contrainte de longueur
        $this->assertStringContainsString(
            'Votre mot de passe doit comporter au moins 8 caractères.',
            $errors[0]->getMessage()
        );
    }

    /**
     * Teste le formulaire avec des données invalides compromises.
     */
    public function testCompromisedPassword(): void
    {
        // Remplacez ceci par un mot de passe souvent utilisé (vous pouvez ajuster cette chaîne en fonction de votre environnement)
        $compromisedPassword = '12345678';

        // Données invalides simulées
        $formData = [
            'plainPassword' => [
                'first' => $compromisedPassword,
                'second' => $compromisedPassword,
            ],
        ];

        // Configure le validateur global pour inclure les contraintes
        $validator = Validation::createValidator();

        $form = $this->factory->create(ChangePasswordFormType::class, null, [
            'validation_groups' => null, // Assure que toutes les contraintes sont appliquées
        ]);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire est invalide avec une contrainte `NotCompromisedPassword`
        $this->assertFalse($form->isValid());
    }
}