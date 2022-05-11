<?php

namespace App\Tests\Service;

use App\Entity\DispatchRequest;
use App\Entity\LogisticProvider;
use App\Exception\ProductNotInStock;
use App\Repository\LogisticProviderRepository;
use App\Repository\StockRepository;
use App\Service\Dispatcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class DispatchTest extends TestCase
{
    use ProphecyTrait;

    public function setUp(): void
    {
        $this->stockRepository = $this->prophesize(StockRepository::class);
        $this->logisticProviderRepository = $this->prophesize(LogisticProviderRepository::class);
        $this->dispatcher = new Dispatcher(
            $this->stockRepository->reveal(),
            $this->logisticProviderRepository->reveal()
        );
    }

    public function test_il_dispatch_par_priorité(): void
    {
        $dispatchRequest = new DispatchRequest();
        $dispatchRequest->setSource('dummy_source');

        $dispatchRequest->setItems([
            [
                "ean" => "3616423828014",
                "quantity" => 1
            ],
            [
                "ean" => "3612887865404",
                "quantity" => 1
            ]
        ]);

        $this->stockRepository->findPrioritizedByEAN(["3616423828014", "3612887865404"])->willReturn(json_decode(
        <<<JSON
        [
            {
                "ean": "3616423828014",
                "quantity": 2,
                "updated_at": "2022-03-14 20:08:26",
                "priority": 100,
                "warehouse_reference": "CLOG"
            },
            {
                "ean": "3612887865404",
                "quantity": 1,
                "updated_at": "2022-03-14 20:08:26",
                "priority": 100,
                "warehouse_reference": "CLOG"
            },
            {
                "ean": "3612887865404",
                "quantity": 127,
                "updated_at": "2022-03-14 20:08:09",
                "priority": 0,
                "warehouse_reference": "ORLI-MAU_RO"
            },
            {
                "ean": "3616423828014",
                "quantity": 5,
                "updated_at": "2022-03-14 20:08:09",
                "priority": 0,
                "warehouse_reference": "ORLI-MAU_RO"
            }
        ]
        JSON, true));

        $stocks = $this->dispatcher->dispatch($dispatchRequest);

        $expected = [
            '3616423828014' => [
                'CLOG' => 1
            ],
            '3612887865404' => [
                'CLOG' => 1
            ]
        ];

        $this->assertEquals($expected, $stocks);
    }
}
