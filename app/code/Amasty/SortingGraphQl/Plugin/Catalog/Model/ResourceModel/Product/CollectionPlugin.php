<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\Catalog\Model\ResourceModel\Product;

use Amasty\SortingGraphQl\Model\Resolver\FeaturedList;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class CollectionPlugin
{
    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Stock $stockHelper
    ) {
        $this->stockHelper = $stockHelper;
    }

    /**
     * Add stock filter to collection.
     *
     * @param Collection $productCollection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(Collection $productCollection, $printQuery = false, $logQuery = false)
    {
        if ($productCollection->getFlag(FeaturedList::AM_FEATURED_WIDGET)) {
            $this->stockHelper->addIsInStockFilterToCollection($productCollection);
        }

        return [$printQuery, $logQuery];
    }
}
