<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RegisterControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null; 
        }
        parent::tearDown();
    }

    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();

        // Ouvrir la page d'inscription
        $client->request('GET', '/register');

        // Vérifie que la réponse est 200 OK
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription'); // Vérifie un élément attendu dans la page
    }

    public function testValidRegistrationFormSubmission(): void
    {
        $client = static::createClient();

        // Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');

        // Remplir le formulaire d'inscription
        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_form[email]' => 'testuser@example.com',
            'registration_form[plainPassword][first]' => 'password',
            'registration_form[plainPassword][second]' => 'password',
        ]);

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé vers la page de confirmation
        $this->assertResponseRedirects('/register/confirmation');

        // Vérifie dans la base de données que l'utilisateur a été créé
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        $this->assertNotNull($user);
        $this->assertSame('testuser@example.com', $user->getEmail());
        $this->assertNotNull($user->getEmailVerificationToken());
    }

    public function testVerifyEmailWithValidToken(): void
    {
        // Ajouter un utilisateur de test avec un token valide
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('hashed_password');
        $user->setEmailVerificationToken('valid_token');
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();

        // Simuler l'accès à la route de vérification avec un token valide
        $client->request('GET', '/verify-email?token=valid_token');

        // Vérifie que l'utilisateur est redirigé vers la page d'accueil
        $this->assertResponseRedirects('/');

        // Recharge l'utilisateur pour vérifier les changements
        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertTrue($updatedUser->isVerified());
        $this->assertNull($updatedUser->getEmailVerificationToken());
    }

    public function testVerifyEmailWithInvalidToken(): void
    {
        $client = static::createClient();

        // Simuler l'accès à la route de vérification avec un token invalide
        $client->request('GET', '/verify-email?token=unknown_token');

        // Vérifie qu'une exception 404 est levée (token non trouvé)
        $this->assertResponseStatusCodeSame(404);
    }
}