<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findWithArticle(string $ean)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('a')
            ->innerJoin('p.article', 'a')
            ->where('p.id = :ean')
            ->setParameter(':ean', $ean)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function findByBrands(array $brands)
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p.id, p.quantityInStock')
            ->innerJoin('p.article', 'a', 'WITH', 'IDENTITY(a.brand) IN (:brands)')
            ->where('p.quantityInStock > 0')
            ->setParameter(':brands', $brands)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function updateStock(array $products)
    {
        $values = [];

        foreach ($products as $product) {
            $values[] = "('{$product['ean13']}', {$product['qty']})";
        }

        if (empty($value)) {
            return;
        }

        $values = implode(',', $values);

        $stmt = $this->_em
            ->getConnection()
            ->executeUpdate("
                UPDATE product SET quantity_in_stock = p.quantity
                FROM (VALUES
                    $values
                ) as p(ean,quantity)
                WHERE id = p.ean
            ");
    }
}
