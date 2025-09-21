<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Inventory;

/**
 * This class now works only in catalogsearch_fulltext indexation process
 * for elasticsearch engine compatibility.
 * @see \Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper\Stock::map
 * @see \Amasty\Sorting\Model\Elasticsearch\SkuRegistry
 */
interface GetQtyInterface
{
    /**
     * @param string $sku
     * @param string $websiteCode
     * @return null|float
     */
    public function execute(string $sku, string $websiteCode): ?float;
}
