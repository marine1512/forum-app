<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Liste des utilisateurs à insérer
        $users = [
            [
                'username' => 'ElsaQueen',
                'email' => 'elsa.qn@mail.com',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'elsa', 
                'is_verified' => true,
                'is_active' => true,
                'email_verification_token' => null,
            ],
            [
                'username' => 'MagicFan92',
                'email' => 'magic92@mail.com',
                'roles' => ['ROLE_USER'],
                'password' => 'magic', 
                'is_verified' => true,
                'is_active' => true,
                'email_verification_token' => null,
            ],
        ];

        // Insertion des utilisateurs
        foreach ($users as $userData) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setIsVerified($userData['is_verified']);
            $user->setIsActive($userData['is_active']);
            $user->setEmailVerificationToken($userData['email_verification_token']);

            // Hashage du mot de passe avec le service de hachage
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        // Enregistrement en base
        $manager->flush();
    }
}