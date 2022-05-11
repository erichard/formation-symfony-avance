<?php

declare(strict_types=1);

namespace App\Message;

final class StockUpdated
{
    public function __construct(
        private string $ean,
        private string $warehouseReference,
        private int $quantityAvailable
    ) {
    }

    public function getEan(): string
    {
        return $this->ean;
    }

    public function getWarehouseReference(): string
    {
        return $this->warehouseReference;
    }

    public function getQuantityAvailable(): int
    {
        return $this->quantityAvailable;
    }
}
