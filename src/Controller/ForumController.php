<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;
use App\Repository\SujetRepository;
use App\Form\SujetType;
use App\Entity\Sujet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class ForumController extends AbstractController
{
    #[Route('/forum', name: 'forum')]
    public function show(Request $request, CategoryRepository $categoryRepository, SujetRepository $sujetRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories et les sujets depuis la base
        $categories = $categoryRepository->findAll();
        $sujets = $sujetRepository->findAll();

        // Créer une nouvelle instance de l'entité Sujet et le formulaire
        $sujet = new Sujet();
        $form = $this->createForm(SujetType::class, $sujet);

        // Gérer la requête (formulaire soumis ou non)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sujet); // Préparer l'enregistrement
            $entityManager->flush(); // Mettre à jour la base de données
            
            // Ajouter un message flash (optionnel)
            $this->addFlash('success', 'Sujet créé avec succès !');

            // Redirection pour éviter la soumission multiple
            return $this->redirectToRoute('forum');
        }
        // Passer les catégories et les sujets à la vue
        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
            'sujets' => $sujets,
            'form' => $form->createView(),
        ]);
    }
}