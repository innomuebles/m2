<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\CatalogInventory\Model\Source;

class Stock
{
    /**
     * @param \Magento\CatalogInventory\Model\Source\Stock $subject
     * @param \Closure $proceed
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir
     *
     * @return $this
     */
    public function aroundAddValueSortToCollection(
        $subject,
        $proceed,
        $collection,
        $dir
    ) {
        // fix magento bug. getting full table name
        $collection->getSelect()->joinLeft(
            ['stock_item_table' => $collection->getResource()->getTable('cataloginventory_stock_item')],
            "e.entity_id=stock_item_table.product_id",
            []
        );
        $collection->getSelect()->order("stock_item_table.qty $dir");

        return $subject;
    }
}
