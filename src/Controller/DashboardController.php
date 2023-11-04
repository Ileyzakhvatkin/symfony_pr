<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'dashboard')]
    public function index(ArticleRepository $articleRepository, LicenseLevelControl $licenseLevelControl): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $authUserId = $authUser->getId();
        $licenseStatus = $licenseLevelControl->update($authUser);

        $lastMonth = [
            'val' => $authUserId,
            'date' => (new Carbon('first day of this month'))->toDateString(),
        ];

        return $this->render('dashboard/dashboard.html.twig', [
            'itemActive' => 1,
            'licenseStatus' => $licenseStatus,
            'allArticles' => $articleRepository->getAllArticleCount($authUserId)[0]['1'],
            'articlesLastMonth' => $articleRepository->getArticleCountFromPeriod($lastMonth)[0]['1'],
            'latestArticle' => $articleRepository->lastAarticle($authUserId)[0],
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
