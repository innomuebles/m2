<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Inventory;

use Amasty\Sorting\Model\ResourceModel\Inventory;
use Magento\Framework\App\ResourceConnection;

class GetSimpleQty
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(ResourceConnection $resourceConnection, Inventory $inventory)
    {
        $this->resourceConnection = $resourceConnection;
        $this->inventory = $inventory;
    }

    /**
     * @param string $productSku
     * @param string $websiteCode
     * @return null|float
     */
    public function execute(string $productSku, string $websiteCode): ?float
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('inventory_source_item'), ['SUM(quantity)'])
            ->where('source_code IN (?)', $this->inventory->getSourceCodes($websiteCode))
            ->where('sku = ?', $productSku)
            ->group('sku');
        $qty = $connection->fetchOne($select);

        return $qty ? (float) $qty : null;
    }
}
