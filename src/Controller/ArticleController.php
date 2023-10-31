<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/dashboard_history/', name: 'db_history')]
    public function articlesList()
    {
        return $this->render('dashboard/history.html.twig');
    }

    #[Route('/dashboard_create_article/', name: 'db_create_article')]
    public function formCreateArticle()
    {
        return $this->render('dashboard/create_article.html.twig');
    }

    #[Route('/dashboard_article_detail/', name: 'db_article_detail')]
    public function showArticle()
    {
        return $this->render('dashboard/article_detail.html.twig');
    }

    #[Route('/dashboard_created_article/', methods: ['POST'], name: 'db_created_module')]
    public function createArticle()
    {
        return $this->json(json_encode(['article' => 'created']));
    }
}
