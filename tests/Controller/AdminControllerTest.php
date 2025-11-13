<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class AdminControllerTest extends WebTestCase
{
    /**
     * Test le tableau de bord de l'administration.
     */
    public function testDashboard(): void
    {
        // Simulation de client
        $client = static::createClient();

        // Simuler une connexion d'utilisateur avec un rôle "ROLE_ADMIN"
        $client->loginUser($this->getAdminUser());

        // Accès à la route admin_dashboard
        $client->request('GET', '/admin/');

        // Vérifie que la réponse est 200 (statut OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifie que certaines statistiques s'affichent sur la page
        $this->assertSelectorTextContains('h1', 'Tableau de bord');
        $this->assertSelectorExists('.card-stats');
    }

    /**
     * Test d'accès refusé à l'administration pour un utilisateur anonyme.
     */
    public function testAccessDeniedForAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        // Vérifie qu'un utilisateur non connecté est redirigé vers la page de connexion
        $this->assertResponseRedirects('/login');
    }

    /**
     * Teste l'affichage des membres dans l'administration.
     */
    public function testMembersList(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdminUser());

        $client->request('GET', '/admin/members');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Liste des membres');
    }

    /**
     * Vérifie la création d'un nouveau membre via formulaire.
     */
    public function testCreateNewMember(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdminUser());

        $crawler = $client->request('GET', '/admin/members/new');

        // Vérifie que le formulaire contient bien les champs nécessaires
        $this->assertSelectorExists('form input[name="user[email]"]');
        $this->assertSelectorExists('form input[name="user[password]"]');

        // Soumission du formulaire avec données valides
        $form = $crawler->selectButton('Créer')->form([
            'user[email]' => 'test@example.com',
            'user[password]' => 'password123'
        ]);

        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé après soumission du formulaire
        $this->assertResponseRedirects('/admin/members');

        // Test que le succès a été flashé
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Membre créé.');
    }

    /**
     * Test de suppression de membre via token CSRF (sécurité).
     */
    public function testDeleteMember(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getAdminUser());

        // Supposons un ID existant (exemple : 1)
        $crawler = $client->request('GET', '/admin/members');

        // Récupération du formulaire de suppression
        $form = $crawler->selectButton('Supprimer')->form([
            '_token' => 'VALID_CSRF_TOKEN_FOR_DELETE' // Génère-le dynamiquement dans un vrai test
        ]);

        $client->submit($form);

        // Vérification de redirection et du flash message
        $this->assertResponseRedirects('/admin/members');
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Membre supprimé.');
    }

    /**
     * Utilitaire pour récupérer un utilisateur administrateur.
     */
    private function getAdminUser()
    {
        // Simule un utilisateur admin (ajuster selon votre entité User et système de sécurité)
        return self::getContainer()->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['roles' => ['ROLE_ADMIN']]);
    }
}