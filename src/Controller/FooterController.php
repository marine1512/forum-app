<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    #[Route('/footer', name: 'footer')]
    public function index(UserRepository $userRepository): Response
    {
        // Compter le nombre total de membres
        $nbMembers = $userRepository->count([]);

        return $this->render('footer.html.twig', [
            'nbMembers' => $nbMembers,
        ]);
    }
}
