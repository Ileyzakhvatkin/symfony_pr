<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
//#[IsGranted("IS_AUTHENTICATED_FULLY")]
class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_api_user')]
    public function index(): JsonResponse
    {
        return $this->json($this->getUser(), 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/headers', name: 'app_api_headers')]
    public function headers(Request $request): JsonResponse
    {
        return $this->json($request->headers);
    }
}
