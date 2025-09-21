<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Test\GraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

class SortingDefaultSortingTest extends GraphQlAbstract
{
    private const MAIN_RESPONSE_KEY = 'defaultSorting';

    /**
     * @group amasty_sorting
     *
     * @magentoConfigFixture default_store amsorting/default_sorting/category_1 price
     */
    public function testSortingDefaultSortingCategory(): void
    {
        $assertResponse = [
            [
                'attribute' => 'price',
                'id' => 'price',
                'sortDirection' => 'ASC',
                'text' => 'Price'
            ]
        ];

        $response = $this->graphQlQuery($this->getQueryCategory());

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertResponse);
    }

    /**
     * @group amasty_sorting
     *
     * @magentoConfigFixture default_store amsorting/default_sorting/search_1 name
     */
    public function testSortingDefaultSortingSearch(): void
    {
        $assertResponse = [
            [
                'attribute' => 'name',
                'id' => 'name',
                'sortDirection' => 'ASC',
                'text' => 'Product Name'
            ]
        ];

        $response = $this->graphQlQuery($this->getQuerySearch());

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_RESPONSE_KEY], $assertResponse);
    }

    private function getQueryCategory(): string
    {
        return <<<QUERY
query {
    defaultSorting(pageType:CATEGORY) {
      attribute
      id
      sortDirection
      text
  }
}
QUERY;
    }

    private function getQuerySearch(): string
    {
        return <<<QUERY
query {
    defaultSorting(pageType:SEARCH) {
      attribute
      id
      sortDirection
      text
  }
}
QUERY;
    }
}
