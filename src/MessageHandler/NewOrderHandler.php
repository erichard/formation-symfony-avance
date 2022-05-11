<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NewOrder;
use App\Message\StockUpdated;
use App\Repository\OrderRepository;
use App\Repository\StockRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class NewOrderHandler implements MessageHandlerInterface
{
    public function __construct(
        private StockRepository $stockRepository,
        private OrderRepository $orderRepository,
        private MessageBusInterface $bus,
    ) {
    }

    public function __invoke(NewOrder $message)
    {
        $order = $this->orderRepository->find($message->getOrderId());

        if (null === $order) {
            throw new UnrecoverableMessageHandlingException();
        }

        foreach ($order->getItems() as $item) {
            $stockAvailable = $this
                ->stockRepository
                ->incrementScheduledStock($item->getProduct(), $item->getWarehouse(), $item->getQuantity());

            if ($stockAvailable > 0) {
                $this->bus->dispatch(new StockUpdated($item->getProduct()->getId(), $item->getWarehouse()->getReference(), $stockAvailable));
            }
        }
    }
}
