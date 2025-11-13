<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{
    public function testForumPageLoads(): void
    {
        $client = static::createClient();

        // Accès à la page du forum
        $crawler = $client->request('GET', '/forum');

        // Vérifie que la réponse est un succès (200 OK)
        $this->assertResponseIsSuccessful();

        // Vérifie la présence des catégories dans la page
        $this->assertSelectorExists('div.categories');
    }

    public function testSujetDetailPageLoads(): void
    {
        $client = static::createClient();

        // Remplacez 1 par un ID valide de sujet dans vos fixtures.
        $crawler = $client->request('GET', '/forum/subject/1');

        // Vérifie que la réponse est un succès
        $this->assertResponseIsSuccessful();

        // Vérifie le contenu attendu (par exemple, un titre de sujet)
        $this->assertSelectorTextContains('h1', 'Détails du sujet');
    }

    public function testSubmitComment(): void
    {
        $client = static::createClient();

        // Simule l'ouverture de la page d'un sujet
        $crawler = $client->request('GET', '/forum/subject/1');

        // Soumet un formulaire avec un commentaire
        $form = $crawler->selectButton('Ajouter le commentaire')->form([
            'text' => 'Ceci est un commentaire de test',
        ]);
        $client->submit($form);

        // Vérifie une redirection après l'envoi du formulaire
        $this->assertResponseRedirects('/forum/subject/1');

        // Suivez la redirection
        $client->followRedirect();

        // Vérifiez que le commentaire a été ajouté
        $this->assertSelectorTextContains('.comment', 'Ceci est un commentaire de test');
    }
}