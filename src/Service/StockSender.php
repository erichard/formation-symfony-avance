<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StockSender
{
    private const UPDATE_ALL_STOCK_URL = '/module/royergestionstocks/webservice?method=updateOwnStocksFromMaster';
    private const UPDATE_STOCK_URL = '/module/royergestionstocks/webservice?method=setStocksOnSlaves';

    public function __construct(
        private ShopRepository $shopRepository,
        private ProductRepository $productRepository,
        private HttpClientInterface $httpClient)
    {
    }

    public function sendStockToShops()
    {
        $shops = $this->shopRepository->findEnabled();

        $responses = [];

        foreach ($shops as $shop) {
            $sizes = $this->productRepository->findByBrands($shop->getBrandsId());

            $stocks = [];
            foreach ($sizes as $size) {
                $stocks[] = [
                    'ean13' => $size['id'],
                    'qty' => $size['quantityInStock'],
                ];
            }

            $responses[] = $this->httpClient->request('POST', $shop->getDomain().self::UPDATE_ALL_STOCK_URL, [
                'body' => [
                    'products' => json_encode($stocks),
                ],
            ]);
        }

        foreach ($responses as $response) {
            $response->getStatusCode();
        }
    }

    public function updateStockForProduct($ean, $quantityAvailable)
    {
        $shops = $this->shopRepository->findShopByProduct($ean);

        $product = $this->productRepository->findWithArticle($ean);

        $responses = [];

        foreach ($shops as $shop) {
            $params = [
                'products' => [
                    [
                        'ean13' => $ean,
                        'reference' => $product->getArticle()->getId(),
                        'quantity' => $quantityAvailable,
                        'clog_quantity' => 0,
                        'isCombinaison' => true,
                    ],
                    [
                        'ean13' => '',
                        'reference' => $product->getArticle()->getId(),
                        'quantity' => $product->getArticle()->getQuantityInStock(),
                        'clog_quantity' => 0,
                        'isCombinaison' => false,
                    ],
                ],
                'import' => false,
            ];

            $responses[] = $this->httpClient->request('POST', $shop->getDomain().self::UPDATE_STOCK_URL, [
                'body' => [
                    json_encode($params),
                ],
            ]);
        }

        foreach ($responses as $response) {
            $content = $response->getStatusCode();
        }
    }
}
