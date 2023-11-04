<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\PaymentRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'dashboard')]
    public function index(
        ArticleRepository $articleRepository,
        PaymentRepository $paymentRepository,
        EntityManagerInterface $em
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();
        $authUserId = $authUser->getId();

        $license = $paymentRepository->getLastPayment($authUser)[0];

        if ( Carbon::parse($license->getFinishedAt())->diffInSeconds(Carbon::now()) >= 0 ) {
            $authUser->setRoles(["ROLE_USER"]);
            $em->persist($authUser);
            $em->flush();
        }

        return $this->render('dashboard/dashboard.html.twig', [
            'itemActive' => 1,
            'licenseType' => $license->getLicenseType(),
            'licenseLastDays' => $license->getFinishedAt(),
            'allArticles' => $articleRepository->getArticleCount($authUserId)[0]['1'],
            'articlesLastMonth' => $articleRepository->getLastMonthArticleCount($authUserId)[0]['1'],
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
