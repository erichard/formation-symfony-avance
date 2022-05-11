<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\NewOrder;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/api/v1/order', methods: ['POST'])]
    public function create(
        Request $request,
        OrderRepository $repository,
        WarehouseRepository $warehouseRepository,
        ProductRepository $productRepository,
        MessageBusInterface $bus
    ): Response {
        $items = json_decode($request->request->get('items'), true);

        $normalized = [];

        foreach ($items as $item) {
            $size = $productRepository->findOneById($item['ean13']);
            $warehouse = $warehouseRepository->findOneByReference($item['warehouse_reference']);

            if (null === $size) {
                throw $this->createNotFoundException("Le produit '{$item['ean13']}' n'existe pas");
            }

            if (null === $warehouse) {
                throw $this->createNotFoundException("L'entrepot '{$item['warehouse_reference']}' n'existe pas");
            }

            $normalized[] = [
                'quantity' => $item['quantity'],
                'warehouse' => $warehouse,
                'product_size' => $size,
            ];
        }

        $order = $repository->createWithItems($normalized);

        $bus->dispatch(new NewOrder($order->getId()));

        return $this->json($order, 201);
    }
}
