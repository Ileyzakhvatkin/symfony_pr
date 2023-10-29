<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function home(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/test/', name: 'app_test_page')]
    public function test(): Response
    {
        return $this->json(['test' => 'test']);
    }

    #[Route('/contacts/', name: 'app_contacts')]
    public function contacts(): Response
    {
        return $this->json(['contacts' => 'Moscow, Saratovskaya']);
    }
}
