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

    #[Route('/dashboard_create_article/', name: 'db_create_article')]
    public function createArticle()
    {
        return $this->render('dashboard/create_article.html.twig');
    }

    #[Route('/dashboard_history/', name: 'db_history')]
    public function history()
    {
        return $this->render('dashboard/history.html.twig');
    }

    #[Route('/dashboard_article_detail/', name: 'db_article_detail')]
    public function showArticle()
    {
        return $this->render('dashboard/article_detail.html.twig');
    }

    #[Route('/dashboard_subscription/', name: 'db_subscription')]
    public function subscription()
    {
        return $this->render('dashboard/subscription.html.twig');
    }

    #[Route('/dashboard_profile/', name: 'db_profile')]
    public function profile()
    {
        return $this->render('dashboard/profile.html.twig');
    }

    #[Route('/dashboard_modules/', name: 'db_modules')]
    public function modules()
    {
        return $this->render('dashboard/modules.html.twig');
    }

}
