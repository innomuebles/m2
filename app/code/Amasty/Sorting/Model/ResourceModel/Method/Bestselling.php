<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

use Magento\Framework\Db\Select;

/**
 * Class Bestselling
 *
 * This class provides an index for the best-selling sorting method.
 */
class Bestselling extends AbstractIndexMethod
{
    /**
     * {@inheritdoc}
     */
    public function getIndexTableName()
    {
        return 'amasty_sorting_bestsellers';
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'qty_ordered';
    }

    /**
     * {@inheritdoc}
     */
    public function doReindex()
    {
        $select = $this->indexConnection->select();
        $needCalculateGrouped = !in_array(
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            $this->getAdditionalData('ignoredProductTypes')
        );

        $select->group(['source_table.store_id', 'order_item.product_id']);

        $columns = [
            'product_id' => 'order_item.product_id',
            'store_id' => 'source_table.store_id',
            $this->getSortingColumnName() => new \Zend_Db_Expr('SUM(order_item.qty_ordered)'),
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
        $this->setColumns($select, $columns);

        $select->useStraightJoin();
        // important!

        $bestsellersInfo = $this->indexConnection->fetchAll($select);

        if ($bestsellersInfo) {
            $this->getConnection()->insertArray($this->getMainTable(), array_keys($columns), $bestsellersInfo);
        }

        if ($needCalculateGrouped) {
            $this->calculateGrouped();
        }
    }

    /**
     * @param Select $select
     * @param array $columns
     */
    private function setColumns($select, $columns)
    {
        $select->reset(Select::COLUMNS)->columns($columns);
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param bool $needCalculateGrouped
     *
     * @return bool
     */
    private function addIgnoreProductTypes($select, $needCalculateGrouped = false)
    {
        if ($this->getAdditionalData('ignoredProductTypes')) {
            $select->where(
                'order_item.product_type NOT IN(?)',
                $this->getAdditionalData('ignoredProductTypes')
            );

            return true;
        }

        if ($needCalculateGrouped && $this->getAdditionalData('productResource')) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product $productResource */
            $productResource = $this->getAdditionalData('productResource');
            $groupedIdsSelect = $productResource->getConnection()->select()->from(
                ['main_table' => $this->getTable('catalog_product_entity')],
                ['entity_id']
            )->where(
                'main_table.type_id = ?',
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
            );
            $groupedIds = $productResource->getConnection()->fetchCol($groupedIdsSelect);
            if ($groupedIds) {
                $select->where(
                    'order_item.product_id NOT IN (?)',
                    $groupedIds
                );
            }

            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return bool
     */
    private function addIgnoreStatus($select)
    {
        $orderStatuses = $this->helper->getScopeValue('bestsellers/exclude');
        if ($orderStatuses) {
            $orderStatuses = explode(',', $orderStatuses);
            $select->where('source_table.status NOT IN(?)', $orderStatuses);

            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return bool
     */
    private function addFromDate($select)
    {
        $period = (int)$this->helper->getScopeValue('bestsellers/best_period');
        if ($period) {
            $from = $this->date->date(
                \Magento\Framework\DB\Adapter\Pdo\Mysql::TIMESTAMP_FORMAT,
                $this->date->timestamp() - $period * 24 * 3600
            );
            $select->where('source_table.created_at >= ?', $from);

            return true;
        }

        return false;
    }

    /**
     * This calculation can be very slow, add Grouped product type to ignore for improve speed
     * Count grouped products ordered qty
     * Sum of all simple qty which grouped by parent product and store
     */
    private function calculateGrouped()
    {
        $collection = $this->getAdditionalData('orderItemCollectionFactory')->create();
        $collection->addFieldToFilter('product_type', \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE);
        $select = $collection->getSelect();
        $select->joinLeft(
            ['source_table' => $this->getTable('sales_order')],
            'main_table.order_id = source_table.entity_id',
            []
        );

        $this->addIgnoreStatus($select);
        $this->addFromDate($select);

        $result = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($collection->getItems() as $item) {
            $config = $item->getProductOptionByCode('super_product_config');
            $groupedId = $config['product_id'];
            $storeId = $item->getStoreId();

            if (!isset($result[$storeId][$groupedId])) {
                $result[$storeId][$groupedId] = 0;
            }
            // Sum of all simple qty which grouped by parent product
            $result[$storeId][$groupedId] += $item->getQtyOrdered();
        }

        if (empty($result)) {
            return;
        }

        $insert = [];
        foreach ($result as $storeId => $itemCounts) {
            foreach ($itemCounts as $productId => $count) {
                $insert[] = [
                    'product_id'                  => $productId,
                    'store_id'                    => $storeId,
                    $this->getSortingColumnName() => $count,
                ];
            }
        }

        $columns = ['product_id', 'store_id', $this->getSortingColumnName()];

        $this->getConnection()->insertArray($this->getMainTable(), $columns, $insert);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $currDir)
    {
        $attributeCode = $this->helper->getScopeValue('bestsellers/best_attr');
        if ($attributeCode) {
            if ($this->helper->isElasticSort()) {
                $collection->addAttributeToSort($attributeCode, $currDir);
            } else {
                $collection->addAttributeToSelect($attributeCode);
                $collection->addOrder($attributeCode, $currDir);
            }
        }

        return parent::apply($collection, $currDir);
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues($storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['product_id', 'value' => 'qty_ordered']
        )->where('store_id = ?', $storeId);

        return $this->getConnection()->fetchPairs($select);
    }
}
