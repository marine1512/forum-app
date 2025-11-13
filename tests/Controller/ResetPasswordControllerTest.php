<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ResetPasswordControllerTest extends WebTestCase
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

    public function testResetPasswordRequestFormLoads(): void
    {
        $client = static::createClient();

        // Accès à la page de demande de réinitialisation
        $crawler = $client->request('GET', '/reset-password/password');

        // Vérifie si le formulaire est accessible
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="reset_password_request_form"]');
    }

    public function testProcessPasswordResetWithValidEmail(): void
    {
        $client = static::createClient();

        // Crée un utilisateur dans la base de données pour tester
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Simuler la soumission du formulaire de réinitialisation
        $crawler = $client->request('GET', '/reset-password/password');
        $form = $crawler->selectButton('Envoyer')->form([
            'reset_password_request_form[email]' => 'user@example.com',
        ]);
        $client->submit($form);

        // Vérifie si l'utilisateur est redirigé vers la page de confirmation
        $this->assertResponseRedirects('/reset-password/check-email');
    }

    public function testProcessPasswordResetWithInvalidEmail(): void
    {
        $client = static::createClient();

        // Soumettre un email qui n'existe pas
        $crawler = $client->request('GET', '/reset-password/password');
        $form = $crawler->selectButton('Envoyer')->form([
            'reset_password_request_form[email]' => 'invalid@example.com',
        ]);
        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé vers la page check-email même avec un email invalide pour éviter les leaks d'informations
        $this->assertResponseRedirects('/reset-password/check-email');
    }

    public function testResetPasswordWithValidToken(): void
    {
        $client = static::createClient();

        // Créer un utilisateur et générer un token de réinitialisation valide
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('hashed_password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Accéder à la page de réinitialisation via un jeton valide
        $crawler = $client->request('GET', '/reset-password/reset/valid_token');
        $this->assertResponseIsSuccessful(); // Vérifie si la page se charge correctement

        // Soumettre le formulaire avec un nouveau mot de passe
        $form = $crawler->selectButton('Réinitialiser')->form([
            'change_password_form[plainPassword][first]' => 'new_password',
            'change_password_form[plainPassword][second]' => 'new_password',
        ]);
        $client->submit($form);

        // Vérifie que l'utilisateur est redirigé après la réinitialisation
        $this->assertResponseRedirects('/');

        // Reload l'utilisateur pour vérifier que le mot de passe a bien changé
        $updatedUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        $this->assertNotEquals('hashed_password', $updatedUser->getPassword());
    }

    public function testResetPasswordWithInvalidToken(): void
    {
        $client = static::createClient();

        // Simuler un jeton invalide en accédant à la route incorrecte
        $client->request('GET', '/reset-password/reset/invalid_token');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND); // Vérifie qu'une 404 est retournée
    }
}