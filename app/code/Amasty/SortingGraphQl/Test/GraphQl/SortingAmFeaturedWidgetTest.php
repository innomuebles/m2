<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Test\GraphQl;

use Amasty\Sorting\Model\Indexer\Summary;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class SortingAmFeaturedWidgetTest extends GraphQlAbstract
{
    private const MAIN_RESPONSE_KEY = 'amfeaturedWidget';

    /**
     * @var Summary
     */
    private $summary;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->summary = $objectManager->get(Summary::class);
        $this->productRepository = $objectManager->get(ProductRepositoryInterface::class);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_products.php
     */
    public function testSortingAmFeaturedWidget(): void
    {
        $this->summary->reindexAll();

        // get main product data
        $productSku = 'am_sort_simple_5';
        $product = $this->productRepository->get($productSku);
        $productId = (int)$product->getId();
        $productName = $product->getName();
        $productUrl = $product->getUrlKey();
        $productPrice = (int)$product->getPrice();

        $variables = [
            'sortBy' => 'price',
            'amsortingSortOrder' => 'desc',
            'productsCount' => 4,
            'conditions' => "[]",
            'showPager' => false,
            'productsPerPage' => 5,
            'currentPage' => 3
        ];

        $assertArrayItem = [
            'id' => $productId,
            'sku' => $productSku,
            'productUrl' => '/' . $productUrl . '.html',
            'thumbnail' => [
                'label' => $productName
            ],
            'name' => $productName,
            'price_range' => [
                'maximum_price' => [
                    'final_price' => [
                        'value' => $productPrice
                    ]
                ],
                'minimum_price' => [
                    'final_price' => [
                        'value' => $productPrice
                    ]
                ]
            ],
            'isSalable' => true,
            'hasRequiredOptions' => false
        ];

        $response = $this->graphQlQuery($this->getQuery(), $variables);

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertCount(4, $response[self::MAIN_RESPONSE_KEY]);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY][1], $assertArrayItem);
        $this->assertArrayHasKey('addToCartUrl', $response[self::MAIN_RESPONSE_KEY][1]);
        $this->assertArrayHasKey('addToCompareParams', $response[self::MAIN_RESPONSE_KEY][1]);
    }

    private function getQuery(): string
    {
        return <<<'QUERY'
query AmFeaturedWidget (
        $sortBy: String!,
        $amsortingSortOrder: String!,
        $productsCount: Int!,
        $conditions: String!,
        $showPager: Boolean,
        $productsPerPage: Int,
        $currentPage: Int
    ) {
    amfeaturedWidget (
        sortBy:$sortBy
        amsortingSortOrder:$amsortingSortOrder
        productsCount:$productsCount
        conditions:$conditions
        showPager:$showPager
        productsPerPage:$productsPerPage
        currentPage:$currentPage
    ) {
        id
        sku
        productUrl
        thumbnail {
            label
        }
        name
        price_range {
            maximum_price {
                final_price {
                    value
                }
            }
            minimum_price {
                final_price {
                    value
                }
            }
        }
        isSalable
        hasRequiredOptions
        addToCartUrl
        addToCompareParams
    }
}
QUERY;
    }
}
