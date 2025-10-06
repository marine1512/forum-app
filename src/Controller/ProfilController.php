<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
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
