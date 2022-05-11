<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;
use function Symfony\Component\String\u;

class LigneImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Ligne';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des lignes
                INSERT INTO ligne (id, name)
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
        $stmt->bindValue(':id', $row['CODE_LIGN_PROD']);
        $stmt->bindValue(':name', u($row['LIBELLE'])->lower()->title());
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_ligne';
    }
}
