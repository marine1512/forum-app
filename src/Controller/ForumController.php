<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;
use App\Repository\SujetRepository;
use App\Form\SujetType;
use App\Entity\Sujet;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class ForumController extends AbstractController
{
    #[Route('/forum', name: 'forum')]
public function show(
    Request $request,
    CategoryRepository $categoryRepository,
    SujetRepository $sujetRepository,
    EntityManagerInterface $entityManager
): Response {
    // Récupérer l'ID de la catégorie sélectionnée dans la requête GET
    $selectedCategoryId = $request->query->get('category_filter');

    // Récupérer toutes les catégories
    $categories = $categoryRepository->findAll();

    // Récupération des sujets
    if ($selectedCategoryId) {
        // Si une catégorie est sélectionnée, on filtre les sujets en fonction de cette catégorie
        $sujets = $sujetRepository->findBy(['category' => $selectedCategoryId]);
    } else {
        // Sinon, récupérer tous les sujets
        $sujets = $sujetRepository->findAll();
    }

    // Créer une nouvelle instance de l'entité Sujet
    $sujet = new Sujet();
    $form = $this->createForm(SujetType::class, $sujet);

    // Gestion de la soumission du formulaire
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Liez le nouveau sujet à la catégorie si elle est définie dans le formulaire
        $entityManager->persist($sujet);
        $entityManager->flush();

        // Message flash (facultatif)
        $this->addFlash('success', 'Sujet créé avec succès !');

        // Redirection pour éviter une double soumission du formulaire
        return $this->redirectToRoute('forum');
    }

    // Rendu de la vue
    return $this->render('forum/index.html.twig', [
        'categories' => $categories,
        'sujets' => $sujets,
        'form' => $form->createView(),
        'selectedCategoryId' => $selectedCategoryId, // Ajout pour pré-sélectionner la catégorie
    ]);
}

#[Route('/forum/subject/{id}', name: 'forum_sujet_detail', requirements: ['id' => '\d+'])]
public function showSubject(
    int $id,
    SujetRepository $sujetRepository,
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    // 1. Récupérer le sujet
    $sujet = $sujetRepository->find($id);
    if (!$sujet) {
        throw $this->createNotFoundException('Sujet non trouvé.');
    }

    // 2. Vérifier si un commentaire a été soumis (via une requête POST)
    if ($request->isMethod('POST')) {
        $text = $request->request->get('text'); // Récupérer le contenu du champ "text"

        // Vérification du texte
        if (!isset($text) || empty($text)) {
            $this->addFlash('error', 'Le champ texte est obligatoire.');
        } else {
            // 3. Créer le commentaire et le lier au sujet
            $comment = new Comment();
            $comment->setText($text);
            $comment->setAuthor($this->getUser() ? $this->getUser()->getUserIdentifier() : 'Anonyme'); // Auteur = utilisateur connecté ou "Anonyme"
            if ($this->getUser()) {$comment->setAuthorUser($this->getUser());}
            $comment->setDate(new \DateTimeImmutable());
            $comment->setSubject($sujet);

            // 4. Persister le commentaire en base et afficher un message de réussite
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');

            // Rediriger pour éviter une resoumission du formulaire
            return $this->redirectToRoute('forum_sujet_detail', ['id' => $id]);
        }
    }

    // 5. Récupérer tous les commentaires (après soumission s’il y en a eu)
    $comments = $sujet->getComments();

    // 6. Rendre la vue Twig
    return $this->render('forum/sujet_detail.html.twig', [
        'subject' => $sujet,
        'comments' => $comments,
    ]);
}
}