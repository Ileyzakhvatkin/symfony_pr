<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/dashboard_profile/', name: 'db_profile')]
    public function profile(): Response
    {
        //$user= $doctrine->getRepository(User::class)->find(1);

        return $this->render('dashboard/profile.html.twig', [
            'user' => 'test',
        ]);
    }

    #[Route('/dashboard_token_update/', name: 'db_token_update', methods: ['PATCH'])]
    public function tokenUpdate(): JsonResponse
    {
        return $this->json(json_encode(['token' => 'updated']));
    }

    #[Route('/dashboard_payment/', name: 'db_payment', methods: ['POST'])]
    public function licensePayment(): JsonResponse
    {
        return $this->json(json_encode(['license' => 'purchased']));
    }
}
