<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ArticleController extends AbstractController
{
    #[Route('/dashboard-history/', name: 'history')]
    public function articlesList(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->articleList($this->getUser()->getId());

        return $this->render('dashboard/history.html.twig', [
            'itemActive' => 3,
            'articles' => $articles,
        ]);
    }

    #[Route('/dashboard-create-article/{id}', name: 'create_article', defaults: ["id" => null])]
    public function formCreateArticle($id, ArticleRepository $articleRepository): Response
    {
        $isBlocked = true;
        /** @var User $authUser */
        $authUser = $this->getUser();
        $rolesArr = $authUser->getRoles();

        if (in_array('ROLE_USER_PRO', $rolesArr) || in_array('ROLE_USER_PLUS', $rolesArr)) {
            $isBlocked = false;
        } else {
            if ($articleRepository->getLastMonthArticleCount($authUser->getId())[0]['1'] > 0) {
                $isBlocked = false;
            }
        }

        if (isset($id)) {
            dd('загружаем данные статьи');
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
        ]);
    }

    #[Route('/dashboard-article-detail/{id}', name: 'article_detail')]
    #[IsGranted('MANAGE', subject: 'article')]
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
