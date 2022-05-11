<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;

class ProductImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Produit';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des tailles
                INSERT INTO product (id, article_id, weight, size)
                VALUES (:ean, :article , :weight, :size)
                ON CONFLICT (id) DO UPDATE SET
                    article_id = excluded.article_id
                    , weight = excluded.weight
                    , size = excluded.size
            ');
    }

    public function isRowImportable(array $row): bool
    {
        return true;
    }

    public function bindValue(Statement $stmt, array $row): void
    {
        $stmt->bindValue(':ean', $row['GENCOD']);
        $stmt->bindValue(':article', $row['CODE_ART_COM'].'-'.$row['CODE_COLM']);
        $stmt->bindValue(':size', $row['TAILLE']);
        $stmt->bindValue(':weight', empty($row['POIDS']) ? null : (int) $row['POIDS']);
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_produit';
    }
}
