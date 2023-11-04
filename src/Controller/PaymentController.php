<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/licence-pay/{type}', name: 'licence_pay', methods: ['GET'])]
    public function update($type, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em): RedirectResponse
    {
        /** @var User $authUser **/
        $authUser = $this->getUser();
        $payment = new Payment();
        $payment->setUser($authUser);
        switch ($type) {
            case 'plus':
                $payment->setLicenseType('Plus');
                $authUser->setRoles(['ROLE_USER', 'ROLE_USER_PLUS']);
                break;
            case 'pro':
                $payment->setLicenseType('Pro');
                $authUser->setRoles(['ROLE_USER', 'ROLE_USER_PRO']);
                break;
        }
        $payment->setFinishedAt(Carbon::now()->addWeek());
        $payment->setCreatedAt(Carbon::now());
        $payment->setUpdatedAt(Carbon::now());
        $em->persist($payment);
        $em->persist($authUser);
        $em->flush();

        return new RedirectResponse($urlGenerator->generate('dashboard'));
    }
}
