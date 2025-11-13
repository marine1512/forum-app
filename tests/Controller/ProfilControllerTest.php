<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class ProfilControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        // Préparez les données nécessaires avant chaque test
        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        // Créer un utilisateur pour le test
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Fake password
        $user->setRoles(['ROLE_USER']);

        // Ajouter des commentaires associés à cet utilisateur
        $comment1 = new Comment();
        $comment1->setText('Premier commentaire');
        $comment1->setAuthor('testuser@example.com');
        $comment1->setDate(new \DateTimeImmutable('-1 hour'));
        $comment1->setAuthorUser($user);

        $comment2 = new Comment();
        $comment2->setText('Deuxième commentaire');
        $comment2->setAuthor('testuser@example.com');
        $comment2->setDate(new \DateTimeImmutable('-2 hours'));
        $comment2->setAuthorUser($user);

        // Persister les données
        $this->entityManager->persist($user);
        $this->entityManager->persist($comment1);
        $this->entityManager->persist($comment2);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // Évite les fuites de mémoire
    }

    public function testRedirectWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        // Accès à la route /profil sans être connecté
        $client->request('GET', '/profil');

        // Vérifier que la redirection se fait vers /login
        $this->assertResponseRedirects('/login');
    }

    public function testProfilPageWithComments(): void
    {
        $client = static::createClient();

        // Simulation de connexion d'utilisateur
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'testuser@example.com']);
        $client->loginUser($user);

        // Accès à la route /profil
        $crawler = $client->request('GET', '/profil');

        // Vérifier que la réponse est 200
        $this->assertResponseIsSuccessful();

        // Vérifier que le nom de l'utilisateur est affiché
        $this->assertSelectorTextContains('.username', 'testuser@example.com');

        // Vérifier que les commentaires sont affichés
        $this->assertSelectorTextContains('.comment', 'Premier commentaire');
        $this->assertSelectorTextContains('.comment', 'Deuxième commentaire');
    }
}