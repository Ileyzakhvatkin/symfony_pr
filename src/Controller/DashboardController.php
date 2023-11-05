<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\PaymentRepository;
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
        $licenseInfo = $licenseLevelControl->update($authUser);

        $lastMonth = [
            'val' => $authUserId,
            'date' => (new Carbon('first day of this month'))->toDateString(),
        ];

        return $this->render('dashboard/dashboard.html.twig', [
            'itemActive' => 1,
            'licenseInfo' => $licenseInfo,
            'allArticles' => $articleRepository->getAllArticleCount($authUserId)[0]['1'],
            'articlesLastMonth' => $articleRepository->getArticleCountFromPeriod($lastMonth)[0]['1'],
            'latestArticle' => $articleRepository->lastAarticle($authUserId) ? $articleRepository->lastAarticle($authUserId)[0] : null,
        ]);
    }

    #[Route('/dashboard-subscription/', name: 'subscription')]
    public function subscription(PaymentRepository $paymentRepository, LicenseLevelControl $licenseLevelControl): Response
    {
        $licenseInfo = $licenseLevelControl->update($this->getUser());

        return $this->render('dashboard/subscription.html.twig', [
            'itemActive' => 4,
            'licenseInfo' => $licenseInfo,
            'payments' => $paymentRepository->getList($this->getUser()->getId()),
        ]);
    }
}
