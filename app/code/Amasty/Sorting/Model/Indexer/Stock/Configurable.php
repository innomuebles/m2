<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Indexer\Stock;

use Magento\ConfigurableProduct\Model\ResourceModel\Indexer\Stock\Configurable as NativeIndexer;
use Zend_Db_Expr;
use Magento\Framework\DB\Select;

class Configurable extends NativeIndexer
{
    /**
     * @inheritdoc
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $select = parent::_getStockStatusSelect($entityIds, $usePrimaryTable);
        $this->calculateDependOnSimples($select);

        return $select;
    }

    /**
     * @param Select $select
     */
    private function calculateDependOnSimples($select)
    {
        $columns = $select->getPart(Select::COLUMNS);
        foreach ($columns as &$column) {
            if (isset($column[2]) && $column[2] == 'qty') {
                $column[1] = new Zend_Db_Expr('SUM(IF(i.stock_status > 0, i.qty, 0))');
            }
            // determine stock status based on simples
//            if (isset($column[2]) && $column[2] == 'status') {
//                $column[1] = new Zend_Db_Expr('MAX(i.stock_status = 1)');
//            }
        }
        $select->setPart(Select::COLUMNS, $columns);
    }
}
