<?php

namespace App\Services;

use App\Entity\Payment;
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
        if (isset($this->paymentRepository->getLastPayment($authUser)[0]) &&
            Carbon::parse($this->paymentRepository->getLastPayment($authUser)[0]->getFinishedAt())->diffInSeconds(Carbon::now()) < 60*60*24*3)
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