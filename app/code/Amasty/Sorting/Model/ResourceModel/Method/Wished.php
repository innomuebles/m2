<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

/**
 * Class Wished
 * Sorting adapter for wished method
 */
class Wished extends AbstractIndexMethod
{
    /**
     * {@inheritdoc}
     */
    public function getIndexTableName()
    {
        return 'amasty_sorting_wished';
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'wished';
    }

    /**
     * {@inheritdoc}
     */
    public function doReindex()
    {
        $select = $this->indexConnection->select();

        $select->group(['source_table.store_id', 'source_table.product_id']);

        $viewsNumExpr = new \Zend_Db_Expr('COUNT(source_table.wishlist_item_id)');

        $columns = [
            'product_id' => 'source_table.product_id',
            'store_id' => 'source_table.store_id',
            'wished' => $viewsNumExpr,
        ];

        $select->from(
            ['source_table' => $this->getTable('wishlist_item')],
            $columns
        );

        $this->addFromDate($select);

        $select->useStraightJoin();

        foreach ($this->getBatchSelectIterator('wishlist_item_id', $select) as $select) {
            $wishedInfo = $this->indexConnection->fetchAll($select);
            if ($wishedInfo) {
                $this->getConnection()->insertOnDuplicate($this->getMainTable(), $wishedInfo, [
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
        $period = (int)$this->helper->getScopeValue('wished/wished_period');
        if ($period) {
            $from = $this->date->date(
                \Magento\Framework\DB\Adapter\Pdo\Mysql::TIMESTAMP_FORMAT,
                $this->date->timestamp() - $period * 24 * 3600
            );
            $select->where('source_table.added_at >= ?', $from);
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues(int $storeId, ?array $entityIds = [])
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['product_id', 'value' => 'wished']
        )->where('store_id = ?', $storeId);

        if (!empty($entityIds)) {
            $select->where('product_id in(?)', $entityIds);
        }

        return $this->getConnection()->fetchPairs($select);
    }
}
