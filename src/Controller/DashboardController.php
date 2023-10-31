<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'db_index')]
    public function index()
    {
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/dashboard_subscription/', name: 'db_subscription')]
    public function subscription()
    {
        return $this->render('dashboard/subscription.html.twig');
    }
}
