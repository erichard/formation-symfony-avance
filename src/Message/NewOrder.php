<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Ulid;

final class NewOrder
{
    public function __construct(private Ulid $orderId)
    {
    }

    public function getOrderId(): Ulid
    {
        return $this->orderId;
    }
}
