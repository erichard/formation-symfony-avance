<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;

class TarifImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Tarif';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des marques
                UPDATE product
                SET
                    prix_vente = :prix_vente
                    ,prix_achat = :prix_achat
                WHERE id = :id
            ');
    }

    public function isRowImportable(array $row): bool
    {
        return 'EUR' === $row['DEVISE'];
    }

    public function bindValue(Statement $stmt, array $row): void
    {
        $stmt->bindValue(':id', $row['GENCOD']);
        $stmt->bindValue(':prix_vente', empty($row['HT']) ? null : (int) $row['HT']);
        $stmt->bindValue(':prix_achat', empty($row['PRIX_ACHAT']) ? null : (int) $row['PRIX_ACHAT']);
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_tarif';
    }
}
