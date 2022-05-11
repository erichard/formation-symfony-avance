<?php

declare(strict_types=1);

namespace App\Clog;

use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\Warehouse;
use App\Repository\ImportJobRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

class StockImporter
{
    private $importJobRepository;

    public const CLOG_PROVIDER_ID = 1;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, ImportJobRepository $importJobRepository)
    {
        $this->em = $em;
        $this->importJobRepository = $importJobRepository;
    }

    public function import(string $filename)
    {
        if (!is_file($filename)) {
            $io->error('Impossible de lire le fichier');

            return Command::FAILURE;
        }

        $job = $this->importJobRepository->createJob('Import Stock CLOG', $filename);

        $date = new \DateTimeImmutable();

        $csv = Reader::createFromPath($filename, 'r');
        $csv->setDelimiter(';');
        $records = $csv->getRecords(['clog', 'code', 'date', 'ean', 'quantity']);

        $warehouseRepository = $this->em->getRepository(Warehouse::class);
        $productRepository = $this->em->getRepository(Product::class);
        $stockRepository = $this->em->getRepository(Stock::class);

        foreach ($records as $lineno => $row) {
            $warehouse = $warehouseRepository->findOneByLogisticProvider(self::CLOG_PROVIDER_ID);
            $product = $productRepository->find($row['ean']);
            if (null === $product) {

                $job->addError([
                    'data' => $row,
                    'lineno' => $lineno,
                    'message' => 'Produit inconnu pour le code EAN : '.$row['ean'],
                ]);

                continue;
            }

            $stock = $stockRepository->findOrCreate($warehouse, $product);
            $stock->setUpdatedAt($date);
            $stock->setQuantityOnHand((int) $row['quantity']);

            $job->increment();
        }
        $this->em->flush();

        $this->importJobRepository->finishJob($job);
    }
}
