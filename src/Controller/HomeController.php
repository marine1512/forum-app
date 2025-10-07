<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\Sujet;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(UserRepository $userRepository, \Doctrine\ORM\EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Sujet::class);
        
        $user = $this->getUser();
        $isUserLoggedIn = $this->isGranted('IS_AUTHENTICATED_FULLY');

        $lastMembers = $userRepository->findBy([], ['id' => 'DESC'], 3);
        $dernieresNouveautes = $entityManager->getRepository(\App\Entity\Sujet::class)
            ->findBy([], ['createdAt' => 'DESC'], 2); 

        $topDiscussed = $repo->findTopDiscussed(5); 


        return $this->render('home/index.html.twig', [
            'user' => $user,
            'isUserLoggedIn' => $isUserLoggedIn,
            'last_members' => $lastMembers,
            'dernieresNouveautes' => $dernieresNouveautes,
            'topDiscussed' => $topDiscussed,
        ]);
    }
    }