<?php

namespace App\DataFixtures;

use App\Entity\Sujet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SujetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Sujets à insérer dans la base
        $sujets = [
            'T’es nouveau ? fait ta présentation',
            'C’est quoi ton film préféré ?',
            'Quel est ton personnage préféré ?',
        ];

        foreach ($sujets as $sujetName) {
            $sujet = new Sujet();
            $sujet->setName($sujetName);
            $manager->persist($sujet); // Prépare l'insertion
        }

        $manager->flush(); // Enregistre en base
    }
}