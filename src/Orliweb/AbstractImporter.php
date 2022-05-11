<?php

declare(strict_types=1);

namespace App\Orliweb;

use App\Repository\ImportJobRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

abstract class AbstractImporter
{
    public function __construct(protected Connection $connection, protected ImportJobRepository $importJobRepository)
    {
    }

    abstract public function getName(): string;

    abstract public function createSQLStatement(): Statement;

    abstract public function isRowImportable(array $row): bool;

    abstract public function bindValue(Statement $stmt, array $row): void;

    abstract public function getSavepointName(array $row): string;

    public function import(string $filename)
    {
        if (!is_file($filename)) {
            throw new FileNotFoundException();
        }

        $job = $this->importJobRepository->createJob('Start import : '.$this->getName(), $filename);

        $this
            ->connection
            ->beginTransaction();

        $encoder = (new CharsetConverter())
            ->inputEncoding('iso-8859-15')
        ;

        $csv = Reader::createFromPath($filename, 'r');
        $csv->addStreamFilter('convert.iconv.ISO-8859-15/UTF-8');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        $stmt = $this->createSQLStatement();

        foreach ($csv as $lineno => $row) {
            if (!$this->isRowImportable($row)) {
                continue;
            }

            $this->bindValue($stmt, $row);

            $savepoint = $this->getSavepointName($row);
            $this->connection->createSavepoint($savepoint);

            try {
                $stmt->execute();
                $job->increment();
                $this->connection->releaseSavepoint($savepoint);
            } catch (\Exception $exception) {
                $this->connection->rollbackSavepoint($savepoint);
                $message = $exception->getMessage();

                if ($exception instanceof ForeignKeyConstraintViolationException) {
                    dump($exception);
                }

                $job->addError([
                    'data' => $row,
                    'lineno' => $lineno,
                    'message' => $message,
                ]);
            }
        }

        $this
            ->connection
            ->commit();

        $this->importJobRepository->finishJob($job);
    }
}
