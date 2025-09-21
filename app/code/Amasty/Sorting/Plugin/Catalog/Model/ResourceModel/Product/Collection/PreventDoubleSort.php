<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Catalog\Model\ResourceModel\Product\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;

class PreventDoubleSort
{
    /**
     * Prevent double sorting by some attribute.
     * Need on some cases with MySQL search engine.
     * @see Collection::addAttributeToSort
     */
    public function aroundAddAttributeToSort(
        Collection $collection,
        callable $proceed,
        string $attribute,
        string $dir = Collection::SORT_ORDER_ASC
    ): Collection {
        if (!$collection->getFlag($this->getFlagName($attribute))
            || $this->isNeedForce($collection, $attribute)
        ) {
            $collection->setFlag($this->getFlagName($attribute), true);
            $proceed($attribute, $dir);
        }

        return $collection;
    }

    private function getFlagName(string $attribute): string
    {
        return sprintf('sorted_by_%s_attribute', $attribute);
    }

    /**
     * We can skip flag check in case when order not appeared in select,
     * because for search engines different MySQL check for double sorting added after setOrder trigger
     * from fulltext collection.
     */
    private function isNeedForce(Collection $collection, string $attribute): bool
    {
        $orders = $collection->getSelect()->getPart(Select::ORDER);
        foreach ($orders as $order) {
            if ($order instanceof \Zend_Db_Expr) {
                $order = $order->__toString();
                $expr = '/(.*\W)(' . Select::SQL_ASC . '|' . Select::SQL_DESC . ')\b/si';
                preg_match($expr, $order, $matches);
                if ($matches) {
                    $orderParts = trim($matches[1]);
                } else {
                    $orderParts = trim($order);
                }
            } elseif (is_array($order) && isset($order[0])) {
                $orderParts = explode('.', $order[0]);
            }
            $orderField = $orderParts[1] ?? $orderParts[0] ?? null;

            if ($orderField === $attribute) {
                return false;
            }
        }

        return true;
    }
}
