<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Inventory\CatalogInventory;

use Amasty\Sorting\Model\Inventory\GetQtyInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class GetConfigurableQty implements GetQtyInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param string $sku
     * @param string $websiteCode
     * @return null|float
     */
    public function execute(string $sku, string $websiteCode): ?float
    {
        $qty = $this->stockRegistry->getStockStatusBySku($sku, $websiteCode)->getQty();
        return $qty !== null ? (float) $qty : null;
    }
}
