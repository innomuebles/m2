<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class MostViewed extends AbstractIndexMethod
{
    /**
     * {@inheritdoc}
     */
    public function getIndexTableName()
    {
        return 'amasty_sorting_most_viewed';
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'views_num';
    }

    /**
     * {@inheritdoc}
     */
    public function doReindex()
    {
        $select = $this->indexConnection->select();

        $select->group(['source_table.store_id', 'source_table.object_id']);

        $viewsNumExpr = new \Zend_Db_Expr('COUNT(source_table.event_id)');

        $columns = [
            'product_id' => 'source_table.object_id',
            'store_id' => 'source_table.store_id',
            'views_num' => $viewsNumExpr,
        ];

        $select->from(
            ['source_table' => $this->getTable('report_event')],
            $columns
        )->where(
            'source_table.event_type_id = ?',
            \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW
        );

        $this->addFromDate($select);

        $havingPart = $this->indexConnection->prepareSqlCondition($viewsNumExpr, ['gt' => 0]);
        $select->having($havingPart);

        $select->useStraightJoin();

        foreach ($this->getBatchSelectIterator('object_id', $select) as $select) {
            $viewedInfo = $this->indexConnection->fetchAll($select);
            if ($viewedInfo) {
                $this->getConnection()->insertOnDuplicate($this->getMainTable(), $viewedInfo, [
                    $this->getSortingColumnName()
                ]);
            }
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     *
     * @return bool
     */
    private function addFromDate($select)
    {
        $period = (int)$this->helper->getScopeValue('most_viewed/viewed_period');
        if ($period) {
            $from = $this->date->date(
                \Magento\Framework\DB\Adapter\Pdo\Mysql::TIMESTAMP_FORMAT,
                $this->date->timestamp() - $period * 24 * 3600
            );
            $select->where('source_table.logged_at >= ?', $from);
            return true;
        }

        return false;
    }

    public function applyCustomAttribute(Collection $collection, string $direction): void
    {
        if ($attributeCode = $this->helper->getScopeValue('most_viewed/viewed_attr')) {
            $collection->setOrder($attributeCode, $direction);
        }
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues(int $storeId, ?array $entityIds = [])
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['product_id', 'value' => 'views_num']
        )->where('store_id = ?', $storeId);

        if (!empty($entityIds)) {
            $select->where('product_id in(?)', $entityIds);
        }

        return $this->getConnection()->fetchPairs($select);
    }
}
