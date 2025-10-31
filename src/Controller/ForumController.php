<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;
use App\Repository\SujetRepository;
use App\Form\SujetType;
use App\Form\CommentType;
use App\Entity\Sujet;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur pour gérer les fonctionnalités du forum.
 */
class ForumController extends AbstractController
{
    /**
     * Affiche la page principale du forum avec les catégories et les sujets.
     *
     * @Route("/forum", name="forum")
     * @param Request $request La requête HTTP.
     * @param CategoryRepository $categoryRepository Le dépôt pour accéder aux catégories.
     * @param SujetRepository $sujetRepository Le dépôt pour accéder aux sujets.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance des données.
     * @return Response La réponse HTTP contenant la vue du forum.
     */
    #[Route('/forum', name: 'forum')]
    public function show(
        Request $request,
        CategoryRepository $categoryRepository,
        SujetRepository $sujetRepository,
        EntityManagerInterface $entityManager
    ): Response {

        $selectedCategoryId = $request->query->get('category_filter');


        $categories = $categoryRepository->findAll();

        if ($selectedCategoryId) {

            $sujets = $sujetRepository->findBy(['category' => $selectedCategoryId]);
        } else {
            $sujets = $sujetRepository->findAll();
        }

        $sujet = new Sujet();
        $form = $this->createForm(SujetType::class, $sujet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sujet);
            $entityManager->flush();

            $this->addFlash('success', 'Sujet créé avec succès !');

            return $this->redirectToRoute('forum');
        }

        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
            'sujets' => $sujets,
            'form' => $form->createView(),
            'selectedCategoryId' => $selectedCategoryId, 
        ]);
    }

    /**
     * Affiche les détails d'un sujet spécifique, y compris les commentaires associés.
     *
     * @Route("/forum/subject/{id}", name="forum_sujet_detail", requirements={"id"="\d+"})
     * @param int $id L'ID du sujet à afficher.
     * @param SujetRepository $sujetRepository Le dépôt pour accéder aux sujets.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance des données.
     * @param Request $request La requête HTTP.
     * @return Response La réponse HTTP contenant la vue du sujet et ses commentaires.
     */
    #[Route('/forum/subject/{id}', name: 'forum_sujet_detail', requirements: ['id' => '\d+'])]
    public function showSubject(
        int $id,
        SujetRepository $sujetRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

        $sujet = $sujetRepository->find($id);
        if (!$sujet) {
            throw $this->createNotFoundException('Sujet non trouvé.');
        }

        if ($request->isMethod('POST')) {
            $text = $request->request->get('text'); 

            if (!isset($text) || empty($text)) {
                $this->addFlash('error', 'Le champ texte est obligatoire.');
            } else {
                $comment = new Comment();
                $comment->setText($text);
                $comment->setAuthor($this->getUser() ? $this->getUser()->getUserIdentifier() : 'Anonyme');
                if ($this->getUser()) {$comment->setAuthorUser($this->getUser());}
                $comment->setDate(new \DateTimeImmutable());
                $comment->setSubject($sujet);

                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Commentaire ajouté avec succès.');

                return $this->redirectToRoute('forum_sujet_detail', ['id' => $id]);
            }
        }

        $comments = $sujet->getComments();

        return $this->render('forum/sujet_detail.html.twig', [
            'subject' => $sujet,
            'comments' => $comments,
        ]);
    }

    /**
     * Permet à un utilisateur de modifier son propre commentaire.
     *
     * @Route("/comment/{id}/edit", name="comment_edit")
     * @param Comment $comment Le commentaire à modifier.
     * @param Request $request La requête HTTP.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour la persistance des données.
     * @return Response La réponse HTTP contenant la vue du formulaire de modification.
     */
    #[Route('/comment/{id}/edit', name: 'comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur connecté est l'auteur
        if ($this->getUser()->getUserIdentifier() !== $comment->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        // Créez un formulaire pour modifier le commentaire
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le commentaire a été modifié avec succès.');

            return $this->redirectToRoute('forum_sujet_detail', ['id' => $comment->getSubject()->getId()]); // Redirigez vers la liste des commentaires
        }

        return $this->render('forum/comment_edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }
}