<?php

namespace App\Services;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class LicenseLevelController
{
    private PaymentRepository $paymentRepository;
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(
        PaymentRepository $paymentRepository,
        EntityManagerInterface $em,
        Security $security
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->em = $em;
        $this->security = $security;
    }

    public function update($authUser = null)
    {
        if (!$authUser) {
            $authUser = $this->security->getUser();
        }

        if (isset($this->paymentRepository->getLastPayment($authUser)[0]))
        {
            /** @var Payment $license */
            $license = $this->paymentRepository->getLastPayment($authUser)[0];
            $licenseType = $license->getLicenseType();
            $licenseStatus = 'Подписка ' . $license->getLicenseType() . ' действует до ' . Carbon::parse($license->getFinishedAt())->toDateTimeString();

            if ( (Carbon::parse($license->getFinishedAt())->getTimestamp() - Carbon::now()->getTimestamp()) < 0 ) {

                $licenseStatus = 'Подписка ' . $license->getLicenseType() . ' закончилась ' . Carbon::parse($license->getFinishedAt())->format('Y-m-d H:i');
                $licenseType = 'FREE';
            }
            return [
                'status' => $licenseStatus,
                'type' => $licenseType,
            ];
        } else {
            return [
                'status' => null,
                'type' => 'FREE',
            ];
        }
    }
}