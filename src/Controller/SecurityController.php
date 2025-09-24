<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // Si l'utilisateur est déjà connecté, il est redirigé
        if ($this->getUser()) {
            return $this->redirectToRoute('home'); // Redirige vers l'accueil
        }

        // Récupère les éventuelles erreurs d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier e-mail ou login saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendu du formulaire de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
        #[Route(path: '/login_check', name: 'app_login_check')]
    public function loginCheck(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by your security system.');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Le processus de déconnexion est géré automatiquement par Symfony via security.yaml
        throw new \LogicException('Cette méthode peut être vide, elle est interceptée par la clé firewall.logout dans security.yaml.');
    }
}
