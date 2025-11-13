<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Category;
use App\Entity\Sujet;

class CategoryTest extends TestCase
{
    public function testCategoryGettersAndSetters(): void
    {
        $category = new Category();

        // Test du setter et getter pour le nom
        $category->setName('Test Category');
        $this->assertEquals('Test Category', $category->getName());

        // Test de l'initialisation de la collection des sujets
        $this->assertCount(0, $category->getSujets());
    }

    public function testAddSujet(): void
    {
        $category = new Category();
        $sujet = new Sujet();

        // Ajouter un sujet à la catégorie
        $category->addSujet($sujet);

        // Vérifier que le sujet est bien ajouté
        $this->assertCount(1, $category->getSujets());
        $this->assertTrue($category->getSujets()->contains($sujet));

        // Vérifier que la catégorie a bien été liée au sujet
        $this->assertSame($category, $sujet->getCategory());
    }

    public function testAddDuplicateSujet(): void
    {
        $category = new Category();
        $sujet = new Sujet();

        // Ajouter deux fois le même sujet
        $category->addSujet($sujet);
        $category->addSujet($sujet);

        // Vérifier qu'il n'est ajouté qu'une seule fois
        $this->assertCount(1, $category->getSujets());
    }

    public function testRemoveSujet(): void
    {
        $category = new Category();
        $sujet = new Sujet();

        // Ajouter puis supprimer un sujet
        $category->addSujet($sujet);
        $category->removeSujet($sujet);

        // Vérifier que le sujet a bien été enlevé
        $this->assertCount(0, $category->getSujets());
        $this->assertNull($sujet->getCategory());
    }
}