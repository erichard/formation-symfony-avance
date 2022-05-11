<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogisticProvider;
use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function getStock(string $ean): int
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('sum(s.quantity_available) as quantity')
            ->where('IDENTITY(s.ean) LIKE :ean')
            ->groupBy('s.ean')
            ->setParameter(':ean', $ean)
            ->getQuery()
        ;

        return $query->getSingleScalarResult();
    }

    public function findPrioritizedByEAN(array $items): iterable
    {
        $qb = $this
            ->_em
            ->getConnection()
            ->createQueryBuilder();

        $results = $qb
            ->select('s.ean, s.quantity_available as quantity, s.updated_at, w.priority + p.priority as priority')
            ->addSelect('w.reference as warehouse_reference')
            ->from('stock', 's')
            ->innerJoin('s', 'warehouse', 'w', 'w.id = s.warehouse_id')
            ->innerJoin('w', 'logistic_provider', 'p', 'w.logistic_provider_id = p.id')
            ->where('s.ean IN (:items)')
            ->andWhere('s.quantity_available > 0')
            ->setParameter('items', $items, Connection::PARAM_STR_ARRAY)
            ->orderBy('priority', 'DESC')
            ->fetchAllAssociative();

        return $results;
    }

    public function getStockForProvider(string $ean, LogisticProvider $provider): int
    {
        return $this
            ->createQueryBuilder('s')
            ->select('sum(s.quantity_available) as quantity')
            ->innerJoin('s.warehouse', 'w', 'WITH', 'w.logisticProvider = :provider')
            ->where('IDENTITY(s.ean) LIKE :ean')
            ->groupBy('s.ean')
            ->setParameter(':ean', $ean)
            ->setParameter(':provider', $provider->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getDetailledStockForProvider(string $ean, LogisticProvider $provider): array
    {
        $results = $this
            ->createQueryBuilder('s')
            ->select('w.name as warehouse, s.quantity_available as quantity')
            ->innerJoin('s.warehouse', 'w', 'WITH', 'w.logisticProvider = :provider')
            ->where('IDENTITY(s.ean) LIKE :ean')
            ->orderBy('w.priority', 'DESC')
            ->setParameter(':ean', $ean)
            ->setParameter(':provider', $provider->getId())
            ->getQuery()
            ->getArrayResult()
        ;

        return array_column($results, 'quantity', 'warehouse');
    }

    public function incrementScheduledStock(Product $product, Warehouse $warehouse, int $quantity): bool|int
    {
        $sql = <<<SQL
            UPDATE stock s
            SET quantity_scheduled = s.quantity_scheduled + :quantity
            WHERE s.warehouse_id = :warehouse
                AND s.ean = :ean
            RETURNING s.quantity_available
        SQL;

        return $this
            ->_em
            ->getConnection()
            ->executeQuery($sql, [
                'quantity' => $quantity,
                'warehouse' => $warehouse->getId(),
                'ean' => $product->getId(),
            ])
            ->fetchOne();
    }

    public function resetStockScheduled()
    {
        $sql = <<<SQL
            UPDATE stock s
            SET quantity_scheduled = 0
            WHERE quantity_scheduled > 0
        SQL;

        return $this
            ->_em
            ->getConnection()
            ->executeQuery($sql);
    }
}
