<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'dashboard')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('dashboard/dashboard.html.twig', [
            'allArticles' => $articleRepository->getArticleCount()[0]['1'],
            'articlesLastMonth' => $articleRepository->getLastMonthArticleCount()[0]['1'],
            'lastArticle' => [],
            'lastDays' => 5,
            'latestArticle' => null,
        ]);
    }

    #[Route('/dashboard-subscription/', name: 'subscription')]
    public function subscription(): Response
    {
        return $this->render('dashboard/subscription.html.twig');
    }
}
