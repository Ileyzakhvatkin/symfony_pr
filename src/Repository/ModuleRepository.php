<?php

namespace App\Repository;

use App\Entity\Module;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function modulesListQuery($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'ASC')
            ;
    }

    public function create($authUser, $data):void
    {
        $module = new Module();
        $module
            ->setTitle($data['title'])
            ->setCode($data['code'])
            ->setUser($authUser)
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now());
        $this->getEntityManager()->persist($module);
        $this->getEntityManager()->flush();
    }
}
