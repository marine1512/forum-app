<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour gérer le profil utilisateur.
 */
class ProfilController extends AbstractController
{
    /**
     * Affiche la page de profil de l'utilisateur avec ses commentaires.
     *
     * @Route("/profil", name="profil")
     * @param CommentRepository $commentRepository Le dépôt pour accéder aux commentaires.
     * @return Response La réponse HTTP contenant la vue du profil.
     */
    #[Route('/profil', name: 'profil')]
    public function index(CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $comments = $commentRepository->findBy(
            ['authorUser' => $user],
            ['date' => 'DESC']
        );

        return $this->render('profil.html.twig', [
            'user' => $user,
            'comments' => $comments,
        ]);
    }
}
