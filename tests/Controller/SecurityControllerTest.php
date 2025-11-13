<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SecurityControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        // Supprimer tous les utilisateurs existants avant les tests
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function testLoginPageLoads(): void
    {
        $client = static::createClient();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Vérifie que la page se charge correctement (statut 200)
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form'); // Vérifie qu'un formulaire est présent
    }

    public function testSuccessfulLogin(): void
    {
        $client = static::createClient();

        // Créer un utilisateur pour le test
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Hacher un mot de passe simulé
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Soumettre le formulaire de connexion avec des données valides
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'test@example.com',
            '_password' => 'password',
        ]);
        $client->submit($form);

        // Vérifie que la connexion réussie redirige vers la page d'accueil
        $this->assertResponseRedirects('/'); // Par exemple, redirection vers 'home'
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();

        // Accéder à la page de connexion
        $crawler = $client->request('GET', '/login');

        // Soumettre le formulaire avec des identifiants invalides
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'invalid@example.com',
            '_password' => 'wrongpassword',
        ]);
        $client->submit($form);

        // Vérifie qu'il n'y a pas de redirection (échec de connexion)
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.error', 'Identifiants invalides'); // Remplacez par le sélecteur exact de votre message d'erreur
    }

    public function testRedirectIfAlreadyLoggedIn(): void
    {
        $client = static::createClient();

        // Créer un utilisateur pour le test
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Hacher un mot de passe simulé
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Simuler la connexion de l'utilisateur
        $client->loginUser($user);

        // Accéder à la page de connexion
        $client->request('GET', '/login');

        // Vérifie que l'utilisateur est redirigé s'il est déjà connecté
        $this->assertResponseRedirects('/'); // Par exemple, redirection vers 'home'
    }

    public function testLogout(): void
    {
        $client = static::createClient();

        // Créer un utilisateur pour le test
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Hacher un mot de passe simulé
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Simuler la connexion de l'utilisateur
        $client->loginUser($user);

        // Accéder à la route de déconnexion
        $client->request('GET', '/logout');

        // Symfony gérera la déconnexion automatiquement, ici, vous vérifierez une redirection
        $this->assertResponseRedirects('/login'); // Ou vers une autre route après déconnexion
    }
}