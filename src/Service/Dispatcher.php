<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\DispatchRequest;
use App\Repository\LogisticProviderRepository;
use App\Repository\StockRepository;

class Dispatcher
{
    private $stockRepository;
    private $logisticProviderRepository;

    public function __construct(StockRepository $stockRepository, LogisticProviderRepository $logisticProviderRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->logisticProviderRepository = $logisticProviderRepository;
    }

    public function dispatch(DispatchRequest $dispatchRequest): array
    {
        $items = $dispatchRequest->getItems();
        $eans = $dispatchRequest->getItemsEANs();

        $stocks = $this->stockRepository->findPrioritizedByEAN($eans);
        $dispatchRequest->setStocks($stocks);

        $warehouse = $this->getWarehouseForUniqueShipping($items, $stocks);

        $dispatchResult = [];

        if (null !== $warehouse) {
            foreach ($items as $item) {
                $dispatchResult[$item['ean']][$warehouse]['quantity'] = $item['quantity'];
            }

            $dispatchRequest->markDecisionPassed(
                sprintf('L\'entrepôt "%s" contient tous les produits de la commande', $warehouse)
            );

            $dispatchRequest->setResult($dispatchResult);

            return $dispatchResult;
        }

        $dispatchRequest->markDecisionFailed('Aucun entrepôt ne contient tous les produits de la commande');

        foreach ($items as $item) {
            $ean = $item['ean'];
            $quantityLeft = $item['quantity'];

            $dispatchResult[$ean] = [];

            foreach ($stocks as $stock) {
                if ($stock['ean'] !== $ean) {
                    continue;
                }

                if (0 === $quantityLeft) {
                    $dispatchRequest->markDecisionFailed(
                        sprintf('Le produit %s est en stock dans l\'entrepôt "%s" avec une priorité %d', $ean, $stock['warehouse_reference'], $stock['priority'])
                    );
                    continue;
                }

                $quantityDispatched = $stock['quantity'] >= $quantityLeft ? $quantityLeft : $stock['quantity'];
                $dispatchResult[$ean][$stock['warehouse_reference']] = [
                    'quantity' => $quantityDispatched,
                ];
                $quantityLeft = $quantityLeft - $quantityDispatched;
                $dispatchRequest->markDecisionPassed(
                    sprintf('Le produit %s est en stock dans l\'entrepôt "%s" avec une priorité %d', $ean, $stock['warehouse_reference'], $stock['priority'])
                );
            }

            if ($quantityLeft > 0) {
                $dispatchRequest->markDecisionFailed('Pas assez de stock pour le produit : '.$ean);
            }
        }

        $dispatchResult = array_filter($dispatchResult);
        $dispatchRequest->setResult($dispatchResult);

        return $dispatchResult;
    }

    private function getWarehouseForUniqueShipping(array $items, array $stocks): ?string
    {
        $warehouseResult = [];
        foreach ($stocks as $stock) {
            $warehouseResult[$stock['warehouse_reference']]['products'][$stock['ean']] = $stock['quantity'];
            $warehouseResult[$stock['warehouse_reference']]['priority'] = $stock['priority'];
        }

        $warehouseResult = array_filter($warehouseResult, function ($products, $ref) use ($items) {
            if (count($products['products']) !== count($items)) {
                return false;
            }

            foreach ($items as $item) {
                if ($item['quantity'] > $products['products'][$item['ean']]) {
                    return false;
                }
            }

            return true;
        }, \ARRAY_FILTER_USE_BOTH);

        uasort($warehouseResult, fn ($a, $b) => $a['priority'] < $b['priority']);

        $warehouse = null;
        if (count($warehouseResult) > 0) {
            $warehouse = array_keys($warehouseResult)[0];
        }

        return $warehouse;
    }
}
