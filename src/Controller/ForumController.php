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
}