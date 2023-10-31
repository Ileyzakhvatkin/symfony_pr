<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/dashboard_profile/', name: 'db_profile')]
    public function profile()
    {
        return $this->render('dashboard/profile.html.twig');
    }

    #[Route('/dashboard_token_update/', methods: ['PATCH'], name: 'db_token_update')]
    public function tokenUpdate()
    {
        return $this->json(json_encode(['token' => 'updated']));
    }

    #[Route('/dashboard_payment/', methods: ['POST'], name: 'db_payment')]
    public function licensePayment()
    {
        return $this->json(json_encode(['license' => 'purchased']));
    }
}
