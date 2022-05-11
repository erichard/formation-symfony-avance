<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ImportJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImportJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportJob[]    findAll()
 * @method ImportJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportJob::class);
    }

    public function createJob(string $title, string $filename): ImportJob
    {
        $job = new ImportJob();
        $job->setTitle($title);
        $job->setCreatedAt(new \DateTimeImmutable());
        $job->setFilename($filename);
        $job->setStatus('started');

        $this->_em->persist($job);
        $this->_em->flush();

        return $job;
    }

    public function finishJob(ImportJob $job)
    {
        $job->setStatus('completed');

        if ($job->hasError()) {
            $job->setStatus('completed_with_errors');
        }

        $job->setFinishedAt(new \DateTimeImmutable());
        $this->_em->flush();
    }
}
