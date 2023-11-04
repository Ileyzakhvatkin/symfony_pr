<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ModuleController extends AbstractController
{
    #[Route('/dashboard-modules/', name: 'modules')]
    public function modules(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->modulesList();

        return $this->render('dashboard/modules.html.twig', [
            'itemActive' => 6,
            'modules' => $modules,
        ]);
    }

//    #[Route('/dashboard-created-modules/', name: 'created_module', methods: ['POST'])]
//    public function createModule(): JsonResponse
//    {
//        return $this->json(json_encode(['module' => 'created']));
//    }
//
//    #[Route('/dashboard-deleted-modules/', name: 'deleted_module', methods: ['DELETE'])]
//    public function deleteModule(): JsonResponse
//    {
//        return $this->json(json_encode(['module' => 'deleted']));
//    }

}
