<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockController extends AbstractController
{
    #[Route('/api/stock/{ean}', name: 'stock')]
    public function hasStock(string $ean, StockRepository $repository): Response
    {
        $stock = $repository->getStock($ean);

        return $this->json([
            'success' => true,
            'message' => 'stock',
            'data' => [
                [
                    'codeLieu' => 'AGG',
                    'codeMagasin' => 'AGG',
                    'quantite' => $stock,
                ],
            ],
        ]);
    }

    #[Route('/fr/module/royergestionstocks/webservice', methods: 'POST')]
    public function update(Request $request, ProductRepository $repository)
    {
        $products = json_decode($request->request->get('products'), true);

        $repository->updateStock($products);

        return $this->json('Done');
    }
}
