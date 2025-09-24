<?php

namespace App\DataFixtures;

use App\Entity\Sujet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SujetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Sujets à insérer dans la base
        $sujets = [
            ['name' => 'T’es nouveau ? fait ta présentation', 'category' => 'category_0'],
            ['name' => 'Comment ça va ?', 'category' => 'category_0'],
            ['name' => 'Tes personnages préférés ?', 'category' => 'category_2'],
            ['name' => 'Les dernières news de Disney', 'category' => 'category_2'],
            ['name' => 'Nouveaux projets Disney', 'category' => 'category_2'],
            ['name' => 'Rumeurs et spéculations', 'category' => 'category_3'],
            ['name' => 'Les classiques Disney', 'category' => 'category_2'],
            ['name' => 'Les films Pixar', 'category' => 'category_2'],
            ['name' => 'Les séries Disney+', 'category' => 'category_2'],
            ['name' => 'Discussions générales sur le TCG', 'category' => 'category_3'],
            ['name' => 'Stratégies et conseils de jeu', 'category' => 'category_3'],
            ['name' => 'Événements et tournois', 'category' => 'category_3'],
            ['name' => 'Astuces pour profiter des parcs', 'category' => 'category_4'],
            ['name' => 'Expériences et souvenirs', 'category' => 'category_4'],
            ['name' => 'Questions sur les parcs', 'category' => 'category_4'],
        ];

        foreach ($sujets as $sujetData) {
            $sujet = new Sujet();
            $sujet->setName($sujetData['name']);

            // Récupérer la référence de la catégorie
            $category = $this->getReference($sujetData['category'], \App\Entity\Category::class);
            $sujet->setCategory($category); // Associer la catégorie au sujet

            $manager->persist($sujet); // Prépare l'insertion
        }

        $manager->flush(); // Enregistre en base
    }

    // Indique que CategoryFixtures doit être chargé avant SujetFixtures
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}