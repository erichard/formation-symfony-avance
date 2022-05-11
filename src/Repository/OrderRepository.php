<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Order $entity, bool $flush = true): void
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
    public function remove(Order $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function createWithItems(array $items)
    {
        $order = new Order();
        $order->setCreatedAt(new \DateTimeImmutable());

        foreach ($items as $item) {
            $orderItem = new OrderItem();
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setWarehouse($item['warehouse']);
            $orderItem->setProduct($item['product_size']);

            $order->addItem($orderItem);

            $this->_em->persist($orderItem);
        }

        $this->add($order, true);

        return $order;
    }
}
