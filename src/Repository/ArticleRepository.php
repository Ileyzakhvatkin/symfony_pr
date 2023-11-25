<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function articleListQuery($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $id)
            ->orderBy('a.createdAt', 'DESC')
        ;
    }

    public function getAllArticleCount($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $id)
            ->select('count(a.id)')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getArticleCountFromPeriod($parameters)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val AND a.createdAt > :date ')
            ->setParameters($parameters)
            ->select('count(a.id)')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getLastArticle($id): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getArticleByIdWithWordsImages($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->leftJoin('a.words', 'w')
            ->addSelect('w')
            ->leftJoin('a.images', 'i')
            ->addSelect('i')
            ->getQuery()
            ->getResult()
        ;
    }

}
