<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\PaymentRepository;
use App\Services\LicenseLevelController;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'dashboard')]
    public function index(
        ArticleRepository      $articleRepository,
        LicenseLevelController $licenseLevelController,
        PaymentRepository      $paymentRepository,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $authUserId = $authUser->getId();

        $lastMonth = [
            'val' => $authUserId,
            'date' => (new Carbon('first day of this month'))->toDateString(),
        ];

        $licenseStatus = null;

        if ( count($paymentRepository->getLastPayment($authUser)) > 0 ) {
            $licenseStatus = Carbon::parse($paymentRepository->getLastPayment($authUser)[0]->getFinishedAt())
                    ->diffInSeconds(Carbon::now()) < 60*60*24*3;
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'itemActive' => 1,
            'licenseStatus' =>  $licenseStatus,
            'licenseInfo' => $licenseLevelController->update($authUser),
            'allArticles' => $articleRepository->getAllArticleCount($authUserId)[0]['1'],
            'articlesLastMonth' => $articleRepository->getArticleCountFromPeriod($lastMonth)[0]['1'],
            'latestArticle' => $articleRepository->getLastArticle($authUserId) ? $articleRepository->getLastArticle($authUserId)[0] : null,
        ]);
    }

    #[Route('/dashboard-subscription/', name: 'subscription')]
    public function subscription(PaymentRepository $paymentRepository, LicenseLevelController $licenseLevelControl): Response
    {

        return $this->render('dashboard/subscription.html.twig', [
            'itemActive' => 4,
            'licenseInfo' => $licenseLevelControl->update($this->getUser()),
            'payments' => $paymentRepository->getList($this->getUser()->getId()),
        ]);
    }

    #[Route('/dashboard-test-api/', name: 'dashboard-test-api')]
    public function testAPI()
    {
        return $this->render('dashboard/testAPI.html.twig', [
            'itemActive' => 4,
        ]);
    }

}
