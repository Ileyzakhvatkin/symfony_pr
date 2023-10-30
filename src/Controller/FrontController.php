<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_homepage')]
    public function home()
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/try/', name: 'front_try')]
    public function try()
    {
        return $this->render('front/try.html.twig');
    }

}
