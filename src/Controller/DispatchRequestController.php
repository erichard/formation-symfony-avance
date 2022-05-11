<?php

declare(strict_types=1);

namespace App\Controller;

use App\Orliweb\StockImporter;
use App\Repository\DispatchRequestRepository;
use App\Service\Dispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DispatchRequestController extends AbstractController
{
    #[Route('/api/dispatchRequest', methods: ['POST'])]
    public function hasStock(Request $request, DispatchRequestRepository $repository, Dispatcher $dispatcher, StockImporter $orliStock): Response
    {
        $items = json_decode($request->request->get('products'), true);

        $normalized = [];
        foreach ($items as $item) {
            $normalized[] = [
                'ean' => $item['ean13'],
                'quantity' => $item['quantity'],
            ];
        }

        $dispatchRequest = $repository->createWithItems($normalized, $request->headers->get('Referer', null));

        if ($request->request->has('pt_result')) {
            $dispatchRequest->setPTResult(json_decode($request->request->get('pt_result'), true));
        }

        if ($request->request->has('pt_duration')) {
            $dispatchRequest->setPTDuration($request->request->getInt('pt_duration'));
        }

        if ($request->request->has('cart_id')) {
            $dispatchRequest->setCartId($request->request->get('cart_id'));
        }

        if ($request->request->has('cart_url')) {
            $dispatchRequest->setCartUrl($request->request->getInt('cart_url'));
        }

        $dispatched = $dispatcher->dispatch($dispatchRequest);

        // Refresh des stocks si dispatch sur Orliweb
        if ($dispatchRequest->isDispatchedFromOrliweb()) {
            $dispatchRequest->markDecisionPassed('Dispatch depuis Orliweb detecté, actualisation des stocks et redispatch');
            $orliStock->importFromAPI($dispatchRequest->getItemsEANs());
            $dispatched = $dispatcher->dispatch($dispatchRequest);
        }

        $repository->finishDispatch($dispatchRequest);

        return $this->json($dispatched);
    }
}
