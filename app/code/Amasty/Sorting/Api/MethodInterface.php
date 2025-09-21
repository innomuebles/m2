<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */
namespace Amasty\Sorting\Api;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Interface IndexedMethodInterface
 * @api
 */
interface MethodInterface
{
    /**
     * Apply sorting method to collection
     *
     * @param Collection $collection
     * @param string $direction
     *
     * @return $this
     */
    public function apply($collection, $direction);

    public function applyCustomAttribute(Collection $collection, string $direction): void;

    /**
     * Returns Sorting method Code for using in code
     *
     * @return string
     */
    public function getMethodCode();

    /**
     * Returns Sorting method Name for using like Method label
     *
     * @return string
     */
    public function getMethodName();

    /**
     * Get method label for store
     *
     * @param null|int|\Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getMethodLabel($store = null);
}
