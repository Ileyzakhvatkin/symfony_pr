<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'dashboard')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('dashboard/dashboard.html.twig', [
            'itemActive' => 1,
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
        //  Подписка Plus оформлена, до 01.01.1970
        return $this->render('dashboard/subscription.html.twig', [
            'itemActive' => 4,
            'sub_info' => null,
        ]);
    }
}
