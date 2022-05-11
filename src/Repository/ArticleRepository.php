<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    function findAllObjects(int $limit)
    {
        return $this->createQueryBuilder('a')
            ->setMaxResults($limit)
            ->orderBy('a.minPrixVente', 'ASC')
            ->getQuery()
            ->getResult();
    }

    function findAllArray(int $limit)
    {
        return $this->createQueryBuilder('a')
            ->setMaxResults($limit)
            ->orderBy('a.minPrixVente', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
