<?php
// src/Controller/LoaderController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoaderController extends AbstractController
{
    #[Route('/', name: 'spinner')]
    public function index(): Response
    {
        return $this->render('loader.html.twig');
    }
}
