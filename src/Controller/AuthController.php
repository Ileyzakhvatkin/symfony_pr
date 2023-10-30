<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/register/', name: 'auth_register')]
    public function register()
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/login/', name: 'auth_login')]
    public function login()
    {
        return $this->render('auth/login.html.twig');
    }

}
