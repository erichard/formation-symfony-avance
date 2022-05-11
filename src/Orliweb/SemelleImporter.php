<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;
use function Symfony\Component\String\u;

class SemelleImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Semelle';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des marques
                INSERT INTO semelle (id, name)
                VALUES (:id, :name)
                ON CONFLICT (id) DO UPDATE SET
                    name = excluded.name
            ');
    }

    public function isRowImportable(array $row): bool
    {
        return true;
    }

    public function bindValue(Statement $stmt, array $row): void
    {
        $stmt->bindValue(':id', $row['CODE_SEM']);
        $stmt->bindValue(':name', u($row['LIBELLE'])->lower()->title());
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_semelle';
    }
}
