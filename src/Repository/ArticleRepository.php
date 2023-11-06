<?php

namespace App\Repository;

use App\Entity\Article;
use Carbon\Carbon;
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

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function articleList($id): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
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

    public function lastAarticle($id): array
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

    public function create($data)
    {
        $article = new Article();
        $article
            ->setTitle($data['title'])
            ->setKeyWord($data['key_word'])
            ->setKeyWordDist($data['key_word_dist'])
            ->setKeyWordMany($data['key_word_many'])
            ->setMinSize($data['min_size'])
            ->setMaxSize($data['max_size'])
            ->setCreatedAt(Carbon::now())
            ->setUpdatedAt(Carbon::now());
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();

        return $id; // Откуда взять ID ???
    }

    public function update($data)
    {

        return $id;
    }

}
