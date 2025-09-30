<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Sujet;
use App\Form\SujetType;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\SujetRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    // -------------------- DASHBOARD --------------------
    #[Route('', name: 'dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        SujetRepository $sujetRepository,
        CommentRepository $commentRepository
    ): Response {
        $stats = [
            'users'      => $userRepository->count([]),
            'categories' => $categoryRepository->count([]),
            'sujets'     => $sujetRepository->count([]),
            'comments'   => $commentRepository->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    // ===================================================
    // ===============      MEMBRES      =================
    // ===================================================

    #[Route('/members', name: 'members')]
    public function members(UserRepository $userRepository): Response
    {
        $members = $userRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/members.html.twig', [
            'members' => $members,
        ]);
    }

    #[Route('/members/new', name: 'members_new')]
    public function membersNew(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();

        // Formulaire minimal (adapte les champs à ton User)
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            // ->add('password') // si tu veux créer un mot de passe ici, gère le hash en service
            ->add('roles') // si ta propriété est array<string>
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ⚠️ Si tu ajoutes un password, pense à le hasher !
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Membre créé.');
            return $this->redirectToRoute('admin_members');
        }

        return $this->render('admin/form_member.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouveau membre',
        ]);
    }

    #[Route('/members/{id}/edit', name: 'members_edit')]
    public function membersEdit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            ->add('roles')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Membre modifié.');
            return $this->redirectToRoute('admin_members');
        }

        return $this->render('admin/form_member.html.twig', [
            'form' => $form->createView(),
            'title' => sprintf('Modifier membre #%d', $user->getId()),
        ]);
    }

    #[Route('/members/{id}/delete', name: 'members_delete', methods: ['POST'])]
    public function membersDelete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Membre supprimé.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_members');
    }

    // ===================================================
    // ===============     CATEGORIES    =================
    // ===================================================

    #[Route('/categories', name: 'categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['id' => 'ASC']);
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/new', name: 'categories_new')]
    public function categoriesNew(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('name')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie créée.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/form_category.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle catégorie',
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'categories_edit')]
    public function categoriesEdit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($category)
            ->add('name')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Catégorie modifiée.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/form_category.html.twig', [
            'form' => $form->createView(),
            'title' => sprintf('Modifier catégorie #%d', $category->getId()),
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'categories_delete', methods: ['POST'])]
    public function categoriesDelete(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_category_'.$category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_categories');
    }

    // ===================================================
    // ===============       SUJETS       ================
    // ===================================================

    #[Route('/sujets', name: 'sujets')]
    public function sujets(SujetRepository $sujetRepository): Response
    {
        $sujets = $sujetRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/sujets.html.twig', [
            'sujets' => $sujets,
        ]);
    }

    #[Route('/sujets/new', name: 'sujets_new')]
    public function sujetsNew(Request $request, EntityManagerInterface $em): Response
    {
        $sujet = new Sujet();
        $form = $this->createForm(SujetType::class, $sujet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sujet);
            $em->flush();
            $this->addFlash('success', 'Sujet créé.');
            return $this->redirectToRoute('admin_sujets');
        }

        return $this->render('admin/form_sujet.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouveau sujet',
        ]);
    }

    #[Route('/sujets/{id}/edit', name: 'sujets_edit')]
    public function sujetsEdit(Sujet $sujet, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SujetType::class, $sujet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Sujet modifié.');
            return $this->redirectToRoute('admin_sujets');
        }

        return $this->render('admin/form_sujet.html.twig', [
            'form' => $form->createView(),
            'title' => sprintf('Modifier sujet #%d', $sujet->getId()),
        ]);
    }

    #[Route('/sujets/{id}/delete', name: 'sujets_delete', methods: ['POST'])]
    public function sujetsDelete(Sujet $sujet, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_sujet_'.$sujet->getId(), $request->request->get('_token'))) {
            $em->remove($sujet);
            $em->flush();
            $this->addFlash('success', 'Sujet supprimé.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin_sujets');
    }
}
