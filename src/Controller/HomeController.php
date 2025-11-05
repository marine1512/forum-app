<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\Sujet;

/**
 * Contrôleur principal pour la gestion de l'accueil du site.
 */
class HomeController extends AbstractController
{
    /**
     * Page d'accueil du site.
     *
     * Ce contrôleur gère l'affichage de plusieurs éléments pour la page d'accueil :
     * - Vérifie si un utilisateur est connecté.
     * - Récupère les trois derniers utilisateurs inscrits.
     * - Affiche les deux dernières nouveautés.
     * - Affiche les sujets les plus discutés.
     *
     * @Route("/", name="home")
     *
     * @param UserRepository $userRepository Instance pour accéder aux entités "User".
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager Instance pour interagir avec la base de données.
     *
     * @return Response Retourne la réponse contenant la vue Twig pour l'affichage de la page d'accueil.
     */
    #[Route('/', name: 'home')]
    public function index(\Doctrine\ORM\EntityManagerInterface $entityManager): Response
    {
        // Récupération du dépôt pour l'entité "Sujet".
        $repo = $entityManager->getRepository(Sujet::class);
        
        // Récupération de l'utilisateur connecté.
        $user = $this->getUser();

        // Vérifie si l'utilisateur est authentifié.
        $isUserLoggedIn = $this->isGranted('IS_AUTHENTICATED_FULLY');


        // Récupération des 2 derniers sujets ajoutés, triés par date de création.
        $dernieresNouveautes = $entityManager->getRepository(\App\Entity\Sujet::class)
            ->findBy([], ['createdAt' => 'DESC'], 2); 

        // Récupération des 5 sujets les plus discutés.
        $topDiscussed = $repo->findTopDiscussed(5); 

        // Rendu de la vue Twig avec les données.
        return $this->render('home/index.html.twig', [
            'user' => $user,
            'isUserLoggedIn' => $isUserLoggedIn,
            'dernieresNouveautes' => $dernieresNouveautes,
            'topDiscussed' => $topDiscussed,
        ]);
    }
}