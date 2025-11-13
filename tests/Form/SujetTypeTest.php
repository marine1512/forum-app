<?php

namespace App\Tests\Form;

use App\Entity\Sujet;
use App\Entity\Category;
use App\Form\SujetType;
use Symfony\Component\Form\Test\TypeTestCase;

class SujetTypeTest extends TypeTestCase
{
    /**
     * Test valide : vérifie que le formulaire accepte des données valides.
     */
    public function testSubmitValidData(): void
    {
        // Données simulées valides
        $formData = [
            'name' => 'Nouveau Sujet',
            'category' => new Category(), // Une instance valide de category
        ];

        // Crée une instance vide de l'entité Sujet pour comparer le résultat
        $sujet = new Sujet();

        // Crée le formulaire
        $form = $this->factory->create(SujetType::class, $sujet);

        // Soumet les données au formulaire
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que les données soumises sont bien mappées à l'entité
        $this->assertEquals('Nouveau Sujet', $sujet->getName());
        $this->assertInstanceOf(Category::class, $sujet->getCategory());

        // Le formulaire doit être valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test valide : vérifie que le formulaire accepte une catégorie null.
     */
    public function testSubmitValidDataWithoutCategory(): void
    {
        // Données simulées sans catégorie
        $formData = [
            'name' => 'Sujet sans catégorie',
            'category' => null,
        ];

        $sujet = new Sujet();
        $form = $this->factory->create(SujetType::class, $sujet);

        // Soumettant les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie les données liées à l'entité
        $this->assertEquals('Sujet sans catégorie', $sujet->getName());
        $this->assertNull($sujet->getCategory());

        // Le formulaire doit être valide
        $this->assertTrue($form->isValid());
    }

    /**
     * Test invalide : vérifie que le champ `name` ne peut pas être vide.
     */
    public function testSubmitInvalidWithoutName(): void
    {
        // Données sans nom
        $formData = [
            'name' => '',
            'category' => new Category(),
        ];

        $sujet = new Sujet();
        $form = $this->factory->create(SujetType::class, $sujet);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le champ `name` est invalide
        $this->assertFalse($form->isValid());

        // Vérifie les messages d'erreur sur le champ `name`
        $errors = $form->get('name')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le nom du sujet ne peut pas être vide.', $errors[0]->getMessage());
    }

    /**
     * Test des options de configuration du formulaire.
     */
    public function testConfigureOptions(): void
    {
        $form = $this->factory->create(SujetType::class);

        // Vérifie que l'option `data_class` est configurée correctement
        $this->assertEquals(Sujet::class, $form->getConfig()->getOption('data_class'));
    }
}