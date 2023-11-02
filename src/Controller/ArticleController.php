<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/dashboard-history/', name: 'history')]
    public function articlesList(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->articleList();

        return $this->render('dashboard/history.html.twig', [
            'itemActive' => 3,
            'articles' => $articles,
        ]);
    }

    #[Route('/dashboard-create-article/{id}', name: 'create_article', defaults: ["id" => null])]
    public function formCreateArticle($id, ArticleRepository $articleRepository): Response
    {
        if (isset($id)) {
            dd('загружаем данные статьи');

        }
        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
        ]);
    }

    #[Route('/dashboard-article-detail/{id}', name: 'article_detail')]
    public function showArticle(Article $article): Response
    {
        return $this->render('dashboard/article_detail.html.twig', [
            'itemActive' => 3,
            'article' => $article,
        ]);
    }

//    #[Route('/dashboard-created-article/', name: 'created_module', methods: ['POST'])]
//    public function createArticle(): JsonResponse
//    {
//        return $this->json(json_encode(['article' => 'created']));
//    }
}
