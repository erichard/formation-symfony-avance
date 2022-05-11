<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DispatchRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DispatchRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method DispatchRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method DispatchRequest[]    findAll()
 * @method DispatchRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DispatchRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DispatchRequest::class);
    }

    public function createWithItems(array $items, string $source = null)
    {
        $dispatchRequest = new DispatchRequest();

        $dispatchRequest->setCreatedAt(new \DateTimeImmutable());
        $dispatchRequest->setItems($items);
        $dispatchRequest->setSource($source);

        return $dispatchRequest;
    }

    public function finishDispatch($dispatchRequest)
    {
        $finishedAt = new \DateTimeImmutable();
        $createdAt = $dispatchRequest->getCreatedAt();
        $diff = $finishedAt->diff($dispatchRequest->getCreatedAt(), true);
        $dispatchRequest->setDuration(round((float) $diff->format('%s.%f'), 3));

        $this->_em->persist($dispatchRequest);
        $this->_em->flush($dispatchRequest);
    }
}
