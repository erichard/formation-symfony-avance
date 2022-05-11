<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;
use function Symfony\Component\String\u;

class BrandImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Marque';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des marques
                INSERT INTO brand (id, name)
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
        $stmt->bindValue(':id', $row['CODE_MARQ']);
        $stmt->bindValue(':name', u($row['LIB'])->lower()->title());
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_marque';
    }
}
