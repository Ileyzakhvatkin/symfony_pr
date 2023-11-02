<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController
{
    #[Route('/dashboard_modules/', name: 'db_modules')]
    public function modules(): Response
    {
        return $this->render('dashboard/modules.html.twig');
    }

    #[Route('/dashboard_created_modules/', name: 'db_created_module', methods: ['POST'])]
    public function createModule(): JsonResponse
    {
        return $this->json(json_encode(['module' => 'created']));
    }

    #[Route('/dashboard_deleted_modules/', name: 'db_deleted_module', methods: ['DELETE'])]
    public function deleteModule(): JsonResponse
    {
        return $this->json(json_encode(['module' => 'deleted']));
    }

}
