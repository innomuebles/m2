<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Test\GraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

class SortingAmOrderListTest extends GraphQlAbstract
{
    private const MAIN_RESPONSE_KEY = 'amOrderList';

    /**
     * @group amasty_sorting
     */
    public function testSortingAmOrderList(): void
    {
        $assertArrayItem = [
            'attribute' => 'price',
            'id' => 'price',
            'sortDirection' => 'ASC',
            'text' => 'Price'
        ];

        $response = $this->graphQlQuery($this->getQuery());

        $this->assertArrayHasKey(self::MAIN_RESPONSE_KEY, $response);
        $this->assertGreaterThanOrEqual(1, count($response[self::MAIN_RESPONSE_KEY]));

        $havePriceAttr = false;
        foreach ($response[self::MAIN_RESPONSE_KEY] as $attr) {
            if ($attr['attribute'] == 'price') {
                $this->assertResponseFields($attr, $assertArrayItem);
                $havePriceAttr = true;
                break;
            }
        }

        $this->assertTrue($havePriceAttr, 'List of orders not contains default attribute "Price".');
    }

    private function getQuery(): string
    {
        return <<<QUERY
query {
    amOrderList {
      attribute
      id
      sortDirection
      text
  }
}
QUERY;
    }
}
