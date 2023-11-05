<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function formCreateArticle($id, ArticleRepository $articleRepository, LicenseLevelControl $licenseLevelControl): Response
    {
        $licenseInfo = $licenseLevelControl->update($this->getUser());
        // Проверяем ограничения на генерацию статей
        $isBlocked = false;
        /** @var User $authUser */
        $authUser = $this->getUser();
        $rolesArr = $authUser->getRoles();
        if ($licenseInfo['type'] == 'PRO') {
            $isBlocked = true;
        } else {
            $parameters = [
                'val' => $authUser->getId(),
                'date' => (new Carbon('-2 hours'))->toDateString(),
            ];
            if ($articleRepository->getArticleCountFromPeriod($parameters)[0]['1'] >= 2) {
                $isBlocked = true;
            }
        }

        if (isset($id)) {
            dd('загружаем данные статьи');
        }

        return $this->render('dashboard/create_article.html.twig', [
            'itemActive' => 2,
            'isBlocked' => $isBlocked,
            'licenseInfo' => $licenseInfo,
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
