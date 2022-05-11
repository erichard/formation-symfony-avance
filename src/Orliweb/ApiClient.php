<?php

declare(strict_types=1);

namespace App\Orliweb;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private $httpClient;

    public function __construct(HttpClientInterface $orliwebClient)
    {
        $this->httpClient = $orliwebClient;
    }

    public function getStockForProducts(array $eans): array
    {
        $responses = [];
        foreach ($eans as $ean) {
            $responses[] = $this->httpClient->request('GET', 'v2/article?ean='.$ean);
        }

        $stocks = [];

        foreach ($this->httpClient->stream($responses) as $response => $chunk) {
            try {
                if ($chunk->isTimeout()) {
                    $response->cancel();
                } elseif ($chunk->isLast()) {
                    $result = $response->toArray();
                    if ('0 enregistrement' === $result['message']) {
                        continue;
                    }

                    $ean = $result['data']['ean'];

                    foreach ($result['data']['stock'] as $stock) {
                        $stocks[] = [
                            'ean' => $ean,
                            'warehouse' => 'ORLI-'.implode('_', [$stock['lieu'], $stock['magasin']]),
                            'quantity' => $stock['quantite'],
                        ];
                    }
                }
            } catch (TransportExceptionInterface $e) {
                continue;
            }
        }

        return $stocks;
    }
}
