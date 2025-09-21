<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Test\GraphQl;

use Amasty\Sorting\Model\Indexer\Summary;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Framework\Shell;

class SortingProductsTest extends GraphQlAbstract
{
    private const MAIN_RESPONSE_KEY = 'products';
    private const MAIN_SEARCH_WORD = 'Amasty';

    /**
     * @var Summary
     */
    private $summary;

    /**
     * @var Shell
     */
    private $shell;

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->summary = $objectManager->get(Summary::class);
        $this->shell = Bootstrap::getObjectManager()->get(Shell::class);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_products.php
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_wishlist.php
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_wishlist_second.php
     */
    public function testSortingProductsWished(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_virtual_1'
                ],
                [
                    'sku' => 'am_sort_simple_4'
                ],
                [
                    'sku' => 'am_sort_virtual_4'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryWished(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_products.php
     */
    public function testSortingProductsPrice(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_virtual_1'
                ],
                [
                    'sku' => 'am_sort_simple_1'
                ],
                [
                    'sku' => 'am_sort_virtual_5'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryPriceAsc(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_products.php
     */
    public function testSortingProductsNew(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_virtual_1'
                ],
                [
                    'sku' => 'am_sort_virtual_4'
                ],
                [
                    'sku' => 'am_sort_virtual_3'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryNew(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_customer_order.php
     */
    public function testSortingProductsBestsellers(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_simple_4'
                ],
                [
                    'sku' => 'am_sort_virtual_2'
                ],
                [
                    'sku' => 'am_sort_virtual_1'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryBestsellers(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_customer_order.php
     */
    public function testSortingProductsRevenue(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_simple_4'
                ],
                [
                    'sku' => 'am_sort_virtual_2'
                ],
                [
                    'sku' => 'am_sort_virtual_1'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryRevenue(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoApiDataFixture Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_customer_order.php
     */
    public function testSortingProductsOtherSorts(): void
    {
        $this->summary->reindexAll();
        $this->reindexCatalogProducts();

        $assertArrayItems = [
            'items' => [
                [
                    'sku' => 'am_sort_virtual_1'
                ],
                [
                    'sku' => 'am_sort_simple_1'
                ],
                [
                    'sku' => 'am_sort_virtual_5'
                ],
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryOtherSorts(self::MAIN_SEARCH_WORD));

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertArrayItems);
    }

    private function getQueryWished(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
        wished:DESC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function getQueryNew(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
        new:DESC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function getQueryPriceAsc(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
         price_desc:ASC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function getQueryBestsellers(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
         bestsellers:DESC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function getQueryRevenue(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
         revenue:DESC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function getQueryOtherSorts(string $search): string
    {
        return <<<QUERY
query {
    products (
      search: "$search"
      pageSize: 3
      sort: {
         rating_summary:ASC
         reviews_count:ASC
         most_viewed:ASC
         saving:ASC
         price_asc:ASC
      }
    ) {
        items {
          sku
        }
    }
}
QUERY;
    }

    private function reindexCatalogProducts():void
    {
        $indexes = [
            'catalogrule_rule',
            'catalogsearch_fulltext',
            'catalog_category_product',
            'catalog_product_attribute',
            'inventory',
            'catalog_product_price',
            'cataloginventory_stock'
        ];
        $parameters = implode(' ', $indexes);

        $appDir = dirname(Bootstrap::getInstance()->getAppTempDir());
        $this->shell->execute("php -f {$appDir}/bin/magento indexer:reindex {$parameters}");
    }
}
