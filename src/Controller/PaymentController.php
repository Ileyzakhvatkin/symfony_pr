<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PaymentRepository;
use App\Services\Mailer;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/licence-pay/{type}', name: 'licence_pay', methods: ['GET'])]
    public function update(
        $type,
        UrlGeneratorInterface $urlGenerator,
        PaymentRepository $paymentRepository,
        Mailer $mailer,
    ): RedirectResponse
    {
        /** @var User $authUser **/
        $authUser = $this->getUser();

        switch ($type) {
            case 'plus':
                $paymentRepository->createPayment('PLUS', $authUser);
                $this->addFlash('flash_message', 'Подписка PLUS оформлена, до ' . Carbon::now()->addWeek());
                $mailer->sendNewPayment($authUser,'PLUS', Carbon::now()->addWeek());
                break;
            case 'pro':
                $paymentRepository->createPayment('PRO', $authUser);
                $this->addFlash('flash_message', 'Подписка PRO оформлена, до ' . Carbon::now()->addWeek());
                $mailer->sendNewPayment($authUser,'PRO', Carbon::now()->addWeek());
                break;
        }

        return new RedirectResponse($urlGenerator->generate('subscription'));
    }
}
