<?php

namespace App\Tests\Form;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryTypeTest extends TypeTestCase
{
    /**
     * Teste que le formulaire soumet des données valides.
     */
    public function testSubmitValidData(): void
    {
        // Données valides
        $formData = [
            'name' => 'Test Category',
        ];

        // Instance initiale de Category en tant que comparaison
        $category = new Category();

        // Crée le formulaire
        $form = $this->factory->create(CategoryType::class, $category);

        // Soumet les données
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que les données soumises sont enregistrées dans l'objet lié
        $this->assertEquals('Test Category', $category->getName());

        // Vérifie que le champ existe dans la vue
        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('name', $children);
    }

    /**
     * Teste que le formulaire rejettera des données invalides (champ vide).
     */
    public function testSubmitInvalidData(): void
    {
        // Données invalides (champ vide)
        $formData = [
            'name' => '',
        ];

        $category = new Category();
        $form = $this->factory->create(CategoryType::class, $category);

        // Soumet les données invalides
        $form->submit($formData);

        // Vérifie que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifie que le formulaire n'est pas valide
        $this->assertFalse($form->isValid());

        // Récupère la liste des erreurs
        $errors = $form->get('name')->getErrors();

        // Vérifie qu'au moins une erreur est présente
        $this->assertCount(1, $errors);

        // Vérifie le message d'erreur
        $this->assertEquals('Le nom de la catégorie ne peut pas être vide.', $errors[0]->getMessage());
    }

    /**
     * Teste les options de configuration du formulaire.
     */
    public function testConfigureOptions(): void
    {
        // Crée une instance du formulaire
        $form = $this->factory->create(CategoryType::class);

        // Vérifie que l'option `data_class` est bien configurée
        $this->assertEquals(Category::class, $form->getConfig()->getOption('data_class'));
    }
}