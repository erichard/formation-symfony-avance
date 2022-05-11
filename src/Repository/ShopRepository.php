<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Shop $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Shop $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findEnabled()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.enabled = true')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findShopByProduct(string $ean)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.brands', 'b')
            ->innerJoin('b.articles', 'a')
            ->innerJoin('a.products', 'p', 'WITH', 'p.id = :ean')
            ->andWhere('s.enabled = true')
            ->setParameter('ean', $ean)
            ->getQuery()
            ->getResult()
        ;
    }
}
