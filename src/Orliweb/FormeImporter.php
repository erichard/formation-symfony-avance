<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;

class FormeImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Forme';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des formes
                INSERT INTO forme (id, name)
                VALUES (:id, :name)
                ON CONFLICT (id) DO UPDATE SET
                    name = excluded.name
            ');
    }

    public function isRowImportable(array $row): bool
    {
        return 'FR' === $row['LANGUE'];
    }

    public function bindValue(Statement $stmt, array $row): void
    {
        $stmt->bindValue(':id', $row['CODE_FORM']);
        $stmt->bindValue(':name', $row['LIBELLE']);
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_forme_'.$row['CODE_FORM'];
    }
}
