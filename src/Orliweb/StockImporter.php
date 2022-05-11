<?php

declare(strict_types=1);

namespace App\Orliweb;

use App\Repository\ImportJobRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use League\Csv\Reader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class StockImporter
{
    private $importJobRepository;
    private $orliApi;

    public const ORLIWEB_ID = 2;
    private Connection $connection;

    public function __construct(Connection $connection, ImportJobRepository $importJobRepository, ApiClient $orliApi)
    {
        $this->connection = $connection;
        $this->importJobRepository = $importJobRepository;
        $this->orliApi = $orliApi;
    }

    public function importFromAPI(array $eans)
    {
        $stocks = $this->orliApi->getStockForProducts($eans);

        $this
            ->connection
            ->beginTransaction();

        $warehouses = $this->getWarehouses();

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

        $date = (new \DateTimeImmutable())->format('c');

        foreach ($stocks as $stock) {
            $stmt->bindValue(':warehouse', $warehouses[$stock['warehouse']]);
            $stmt->bindValue(':ean', $stock['ean']);
            $stmt->bindValue(':date', $date);
            $stmt->bindValue(':quantity', $stock['quantity']);

            $savepoint = 'save_'.$stock['ean'].'_'.$warehouses[$stock['warehouse']];
            $this->connection->createSavepoint($savepoint);

            try {
                $stmt->execute();
                $this->connection->releaseSavepoint($savepoint);
            } catch (\Exception $exception) {
                dump($exception);
                $this->connection->rollbackSavepoint($savepoint);
            }
        }

        $this->connection->executeUpdate(
            'DELETE FROM stock s WHERE s.warehouse_id IN (:warehouses) AND s.updated_at < (:date)',
            ['warehouses' => array_values($warehouses), 'date' => $date],
            ['warehouses' => Connection::PARAM_STR_ARRAY]
        );

        $this
            ->connection
            ->commit();
    }

    public function importFromFile(string $filename)
    {
        if (!is_file($filename)) {
            throw new FileNotFoundException();
        }

        $job = $this->importJobRepository->createJob('Import Stock Orliweb', $filename);

        $warehouses = $this->getWarehouses();

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
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        foreach ($csv as $lineno => $data) {
            if ('O' !== $data['actif']) {
                continue;
            }
            $reference = 'ORLI-' . $data['code_lieu'] . '_' . $data['code_magp'];

            if (!isset($warehouses[$reference])) {
                $warehouses[$reference] = $this->createWarehouse($reference);
            }

            $stmt->bindValue(':warehouse', $warehouses[$reference]);
            $stmt->bindValue(':ean', $data['gen']);
            $stmt->bindValue(':date', $date);
            $stmt->bindValue(':quantity', (int) $data['dispo']);

            $savepoint = 'save_'.$data['gen'].'_'.$warehouses[$reference];

            $this->connection->createSavepoint($savepoint);

            try {
                $stmt->execute();
                $job->increment();
                $this->connection->releaseSavepoint($savepoint);
            } catch (\Exception $exception) {
                $this->connection->rollbackSavepoint($savepoint);
                $message = $exception->getMessage();

                if ($exception instanceof ForeignKeyConstraintViolationException) {
                    $message = 'Produit inconnu pour le code EAN : '.$data['gen'];
                }

                $job->addError([
                    'data' => $data,
                    'lineno' => $lineno,
                    'message' => $message,
                ]);
            }
        }

        $this->connection->executeUpdate(
            'DELETE FROM stock s WHERE s.warehouse_id IN (:warehouses) AND s.updated_at < (:date)',
            ['warehouses' => array_values($warehouses), 'date' => $date],
            ['warehouses' => Connection::PARAM_STR_ARRAY]
        );

        $this
            ->connection
            ->commit();

        $this->importJobRepository->finishJob($job);
    }

    private function getWarehouses(): array
    {
        $stmt = $this->connection->executeQuery(
            'SELECT * FROM warehouse WHERE logistic_provider_id = :provider',
            ['provider' => self::ORLIWEB_ID]
        );

        $warehouses = [];
        foreach ($stmt->fetchAll() as $row) {
            $warehouses[$row['reference']] = $row['id'];
        }

        return $warehouses;
    }

    private function createWarehouse($reference): int
    {
        $w = $this
            ->connection
            ->executeQuery('INSERT INTO warehouse (reference, name, logistic_provider_id) VALUES (:reference, :name, :logistic_provider_id) RETURNING id',
            ['reference' => $reference, 'name' => $reference, 'logistic_provider_id' => self::ORLIWEB_ID])
            ->fetch();

        return $w['id'];
    }
}
