<?php

namespace App\Services;

use App\Entity\Module;
use App\Services\Constants\DemoModules;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class DemoModuleSaver
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create($user) {
        foreach (DemoModules::getModules() as $key=>$el) {
            $module = (new Module())
                ->setTitle(DemoModules::getModules()[$key]['title'])
                ->setUser($user)
                ->setCode(DemoModules::getModules()[$key]['code'])
                ->setCommon(true)
                ->setTwig(DemoModules::getModules()[$key]['file'])
                ->setCreatedAt(Carbon::now())
                ->setUpdatedAt(Carbon::now())
            ;
            $this->em->persist($module);
        }
        $this->em->flush();
    }
}