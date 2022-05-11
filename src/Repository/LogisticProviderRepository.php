<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogisticProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogisticProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogisticProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogisticProvider[]    findAll()
 * @method LogisticProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogisticProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogisticProvider::class);
    }

    public function findAllPrioritized(): iterable
    {
        return $this->findBy([], ['priority' => 'desc']);
    }
}
