<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\StockUpdated;
use App\Service\StockSender;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class StockUpdatedHandler implements MessageHandlerInterface
{
    public function __construct(private StockSender $stockSender)
    {
    }

    public function __invoke(StockUpdated $message)
    {
        $this->stockSender->updateStockForProduct($message->getEan(), $message->getQuantityAvailable());
    }
}
