<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sujet;
use App\Entity\User;

class HomeControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // Évite les problèmes de mémoire
    }

    public function testHomePageLoadsSuccessfully(): void
    {
        // Créez une requête HTTP pour la route "/"
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Vérifiez que la réponse est un succès (200)
        $this->assertResponseIsSuccessful();

        // Vérifiez que la page d'accueil contient certains éléments clés
        $this->assertSelectorTextContains('h1', 'Bienvenue'); // Exemple de texte attendu dans votre Twig
    }

    public function testLatestSujetsAreListed(): void
    {
        // Chargez quelques entités Sujet dans la base de données
        $this->loadSujetFixtures();

        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Vérifiez qu'au moins un sujet récemment ajouté est affiché
        $this->assertSelectorExists('.latest-news'); // Vérifie la présence d'une section des dernières nouveautés
        $this->assertSelectorTextContains('.latest-news', 'Sujet récent 1');
    }

    private function loadSujetFixtures(): void
    {
        // Ajout de deux sujets pour les tests
        $sujet1 = new Sujet();
        $sujet1->setName('Sujet récent 1');
        $sujet1->setCreatedAt(new \DateTimeImmutable('-1 day'));
        $this->entityManager->persist($sujet1);

        $sujet2 = new Sujet();
        $sujet2->setName('Sujet récent 2');
        $sujet2->setCreatedAt(new \DateTimeImmutable('-2 days'));
        $this->entityManager->persist($sujet2);

        $this->entityManager->flush();
    }
}