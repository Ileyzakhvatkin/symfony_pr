<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController
{
    #[Route('/dashboard_modules/', name: 'db_modules')]
    public function modules()
    {
        return $this->render('dashboard/modules.html.twig');
    }

    #[Route('/dashboard_created_modules/', methods: ['POST'], name: 'db_created_module')]
    public function createModule()
    {
        return $this->json(json_encode(['module' => 'created']));
    }

    #[Route('/dashboard_deleted_modules/', methods: ['DELETE'], name: 'db_deleted_module')]
    public function deleteModule()
    {
        return $this->json(json_encode(['module' => 'deleted']));
    }

}
