<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository): Response
    {

        $user = $this->getUser();
        $isUserLoggedIn = $this->isGranted('IS_AUTHENTICATED_FULLY');

        $lastMembers = $userRepository->findBy([], ['id' => 'DESC'], 3);


        return $this->render('home/index.html.twig', [
            'user' => $user,
            'isUserLoggedIn' => $isUserLoggedIn,
            'last_members' => $lastMembers,
        ]);
    }
}