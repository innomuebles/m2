<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Db\Select;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;

class OrderBasedSorting extends AbstractIndexMethod
{
    public const PRODUCT_ID = 'product_id';

    public const STORE_ID = 'store_id';

    public function getSortingColumnName(): string
    {
        return $this->getAdditionalData('sortingColumn');
    }

    public function getOrderColumnName(): string
    {
        return $this->getAdditionalData('orderColumn');
    }

    public function doReindex(): void
    {
        $needCalculateGrouped = !in_array(
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            $this->getAdditionalData('ignoredProductTypes')
        );
        $select = $this->indexConnection->select();
        $select->group(['source_table.store_id', 'order_item.product_id']);

        $columns = [
            self::PRODUCT_ID => 'order_item.product_id',
            self::STORE_ID => 'source_table.store_id',
            $this->getSortingColumnName() =>
                new \Zend_Db_Expr(sprintf('SUM(order_item.%s)', $this->getOrderColumnName())),
        ];

        $select->from(
            ['source_table' => $this->getTable('sales_order')]
        )->joinInner(
            ['order_item' => $this->getTable('sales_order_item')],
            'order_item.order_id = source_table.entity_id',
            []
        )->joinLeft(
            ['order_item_parent' => $this->getTable('sales_order_item')],
            'order_item.parent_item_id = order_item_parent.item_id',
            []
        );

        $this->addIgnoreProductTypes($select, $needCalculateGrouped);
        $this->addIgnoreStatus($select);
        $this->addFromDate($select);
        $select->reset(Select::COLUMNS)->columns($columns);
        $select->useStraightJoin();

        foreach ($this->getBatchSelectIterator('entity_id', $select) as $select) {
            $resultInfo = $this->indexConnection->fetchAll($select);
            if ($resultInfo) {
                $this->insertData($resultInfo);
            }
        }

        if ($needCalculateGrouped) {
            $this->calculateGrouped();
        }
    }

    private function insertData(array $data): void
    {
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
    }

    private function addIgnoreStatus(Select $select): void
    {
        $excludedOrderStatuses =  $this->getExcludedOrderStatuses();

        if ($excludedOrderStatuses) {
            $select->where('source_table.status NOT IN(?)', $excludedOrderStatuses);
        }
    }

    private function getExcludedOrderStatuses(): array
    {
        return $this->getSortingColumnName() === 'revenue'
            ? $this->configProvider->getExcludedOrderStatusesForRevenue()
            : $this->configProvider->getExcludedOrderStatusesForBestsellers();
    }

    private function getPeriod(): int
    {
        return $this->getSortingColumnName() === 'revenue'
            ? $this->configProvider->getRevenuePeriod()
            : $this->configProvider->getBestsellersPeriod();
    }

    private function getSortingAttributeCode(): string
    {
        return $this->getSortingColumnName() === 'revenue'
            ? $this->configProvider->getRevenueAttributeCode()
            : $this->configProvider->getBestsellerAttributeCode();
    }

    private function addFromDate(Select $select): void
    {
        $period = $this->getPeriod();

        if ($period) {
            $from = $this->date->date(
                Mysql::TIMESTAMP_FORMAT,
                $this->date->timestamp() - $period * 24 * 3600
            );
            $select->where('source_table.created_at >= ?', $from);
        }
    }

    /**
     * Count grouped products ordered qty
     * Sum of all simple qty which grouped by parent product and store
     */
    private function calculateGrouped(): void
    {
        $collection = $this->getAdditionalData('orderItemCollectionFactory')->create();
        $collection->addFieldToFilter('product_type', Grouped::TYPE_CODE);
        $select = $collection->getSelect();
        $select->joinLeft(
            ['source_table' => $this->getTable('sales_order')],
            'main_table.order_id = source_table.entity_id',
            []
        );

        $this->addIgnoreStatus($select);
        $this->addFromDate($select);
        $result = $this->calculateItemsQty($collection);

        if (empty($result)) {
            return;
        }

        $insert = [];

        foreach ($result as $storeId => $itemCounts) {
            foreach ($itemCounts as $productId => $count) {
                $insert[] = [
                    self::PRODUCT_ID => $productId,
                    self::STORE_ID => $storeId,
                    $this->getSortingColumnName() => $count,
                ];
            }
        }

        $columns = [self::PRODUCT_ID, self::STORE_ID, $this->getSortingColumnName()];
        $this->getConnection()->insertArray($this->getMainTable(), $columns, $insert);
    }

    private function calculateItemsQty(OrderItemCollection $collection): array
    {
        $result = [];

        foreach ($collection->getItems() as $item) {
            $config = $item->getProductOptionByCode('super_product_config');
            $groupedId = $config[self::PRODUCT_ID];
            $storeId = $item->getStoreId();

            if (!isset($result[$storeId][$groupedId])) {
                $result[$storeId][$groupedId] = 0;
            }
            // Sum of all simple qty which grouped by parent product
            $result[$storeId][$groupedId] += $item->getQtyOrdered();
        }

        return $result;
    }

    private function addIgnoreProductTypes(
        Select $select,
        bool $needCalculateGrouped = false
    ): void {
        $select->where('order_item.product_type NOT IN(?)', $this->getAdditionalData('ignoredProductTypes'));

        if ($needCalculateGrouped) {
            $connection = $this->getConnection();

            $groupedIdsSelect = $connection->select()->from(
                ['main_table' => $this->getTable('catalog_product_entity')],
                ['entity_id']
            )->where(
                'main_table.type_id = ?',
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
            );
            $groupedIds = $connection->fetchCol($groupedIdsSelect);

            if ($groupedIds) {
                $select->where(
                    'order_item.product_id NOT IN (?)',
                    $groupedIds
                );
            }
        }
    }

    public function applyCustomAttribute(Collection $collection, string $direction): void
    {
        if ($attributeCode = $this->getSortingAttributeCode()) {
            $collection->setOrder($attributeCode, $direction);
        }
    }

    public function getIndexedValues(int $storeId, ?array $entityIds = []): array
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            [self::PRODUCT_ID, 'value' => $this->getSortingColumnName()]
        )->where(
            sprintf('%s = ?', self::STORE_ID),
            $storeId
        );

        if (!empty($entityIds)) {
            $select->where(
                sprintf('%s in(?)', self::PRODUCT_ID),
                $entityIds
            );
        }

        return $this->getConnection()->fetchPairs($select);
    }
}
