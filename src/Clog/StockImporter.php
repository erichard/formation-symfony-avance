<?php

declare(strict_types=1);

namespace App\Clog;

use App\Repository\ImportJobRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use League\Csv\Reader;

class StockImporter
{
    private $importJobRepository;

    public const CLOG_PROVIDER_ID = 1;
    private Connection $connection;

    public function __construct(Connection $connection, ImportJobRepository $importJobRepository)
    {
        $this->connection = $connection;
        $this->importJobRepository = $importJobRepository;
    }

    public function import(string $filename)
    {
        if (!is_file($filename)) {
            $io->error('Impossible de lire le fichier');

            return Command::FAILURE;
        }

        $job = $this->importJobRepository->createJob('Import Stock CLOG', $filename);

        $warehouseId = $this->getWarehouseId();

        $this
            ->connection
            ->beginTransaction();

        $date = (new \DateTimeImmutable())->format('c');

        $stmt = $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour du stock
                INSERT INTO stock (ean, warehouse_id, quantity_on_hand, updated_at)
                VALUES (:ean, :warehouse, :quantity, :date)
                ON CONFLICT (ean, warehouse_id) DO UPDATE
                    SET quantity_on_hand = excluded.quantity_on_hand,
                        updated_at = excluded.updated_at
            ');

        $csv = Reader::createFromPath($filename, 'r');
        $csv->setDelimiter(';');
        $records = $csv->getRecords(['clog', 'code', 'date', 'ean', 'quantity']);

        foreach ($records as $lineno => $data) {
            if ('0' === $data['quantity']) {
                continue;
            }

            $stmt->bindValue(':warehouse', $warehouseId);
            $stmt->bindValue(':ean', $data['ean']);
            $stmt->bindValue(':date', $date);
            $stmt->bindValue(':quantity', (int) $data['quantity']);

            $savepoint = 'save_'.$data['ean'].'_'.$warehouseId;

            $this->connection->createSavepoint($savepoint);

            try {
                $stmt->execute();
                $job->increment();
                $this->connection->releaseSavepoint($savepoint);
            } catch (\Exception $exception) {
                $this->connection->rollbackSavepoint($savepoint);
                $message = $exception->getMessage();

                if ($exception instanceof ForeignKeyConstraintViolationException) {
                    $message = 'Produit inconnu pour le code EAN : '.$data['ean'];
                }

                $job->addError([
                    'data' => $data,
                    'lineno' => $lineno,
                    'message' => $message,
                ]);
            }
        }

        $this->connection->executeUpdate(
            'DELETE FROM stock s WHERE s.warehouse_id = :warehouse AND s.updated_at < :date',
            ['warehouse' => $warehouseId, 'date' => $date]
        );

        $this
            ->connection
            ->commit();

        $this->importJobRepository->finishJob($job);
    }

    private function getWarehouseId(): ?int
    {
        $stmt = $this->connection->executeQuery(
            'SELECT id FROM warehouse WHERE logistic_provider_id = :provider',
            ['provider' => self::CLOG_PROVIDER_ID]
        );

        if ($row = $stmt->fetch()) {
            return (int) $row['id'];
        }

        return null;
    }
}
