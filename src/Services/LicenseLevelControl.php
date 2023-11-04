<?php

namespace App\Services;

use App\Repository\PaymentRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class LicenseLevelControl
{
    private PaymentRepository $paymentRepository;
    private EntityManagerInterface $em;

    public function __construct(PaymentRepository $paymentRepository, EntityManagerInterface $em)
    {
        $this->paymentRepository = $paymentRepository;
        $this->em = $em;
    }

    public function update($authUser)
    {
        $license = $this->paymentRepository->getLastPayment($authUser)[0];
        $licenseStatus = 'Подписка ' . $license->getLicenseType() . ' истекает ' . Carbon::parse($license->getFinishedAt())->toDateTimeString();

        if ( Carbon::parse($license->getFinishedAt())->diffInSeconds(Carbon::now()) >= 0 ) {
            $authUser->setRoles(["ROLE_USER"]);
            $this->em->persist($authUser);
            $this->em->flush();
            $licenseStatus = 'Подписка ' . $license->getLicenseType() . ' закончилась ' . Carbon::parse($license->getFinishedAt())->format('Y-m-d H:i');
        }

        return $licenseStatus;
    }
}