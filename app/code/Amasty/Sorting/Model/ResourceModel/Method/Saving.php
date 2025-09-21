<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;

class Saving extends AbstractMethod
{
    /**
     * @var bool
     */
    private $limitColumns = false;

    /**
     * @param Collection $collection
     * @param $direction
     * @param bool $limitColumns
     * @return $this|AbstractMethod
     */
    public function apply($collection, $direction)
    {
        $connection = $this->getConnection();
        $collection->addPriceData();
        $table      = $this->getPriceAlias($collection);

        /** LEAST(min_price, tier_price) */
        $least = $connection->getLeastSql(["$table.min_price", "$table.tier_price"]);
        $price = $table . '.price';
        /** tier_price IS NOT NULL */
        $tpNotNull = $connection->prepareSqlCondition("$table.tier_price", ['notnull' => true]);
        /** IF (tier_price IS NOT NULL, LEAST(min_price, tier_price), min_price) */
        $minPrice = $connection->getCheckSql($tpNotNull, $least, "$table.min_price");

        if ($this->helper->getScopeValue('saving/saving')) {
            $percent = "($price - $minPrice) * 100 / $price";
            $saving  = $connection->getCheckSql($price, $percent, 0);
        } else {
            $saving = "($price - $minPrice)";
        }

        if ($this->isLimitColumns()) {
            $collection->getSelect()->reset(Select::COLUMNS);
            $collection->getSelect()->columns(['product_id' => 'e.entity_id']);
            $alias = 'value';
        } else {
            $alias = $this->getMethodCode();
        }

        $collection->getSelect()->columns([$alias => new \Zend_Db_Expr($saving)]);

        if (!$this->isLimitColumns()) {
            $collection->addExpressionAttributeToSelect($this->getMethodCode(), $this->getMethodCode(), []);

            // remove last item from columns because e.saving from addExpressionAttributeToSelect not exist
            $columns = $collection->getSelect()->getPart(Select::COLUMNS);
            array_pop($columns);
            $collection->getSelect()->setPart(Select::COLUMNS, $columns);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->getMethodCode();
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return string
     */
    private function getPriceAlias($collection)
    {
        $tableAliases = array_keys($collection->getSelect()->getPart(Select::FROM));
        if (in_array($collection::INDEX_TABLE_ALIAS, $tableAliases)) {
            return $collection::INDEX_TABLE_ALIAS;
        }

        return reset($tableAliases);
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues(int $storeId, ?array $entityIds = [])
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isLimitColumns()
    {
        return $this->limitColumns;
    }

    /**
     * @param bool $limitColumns
     */
    public function setLimitColumns($limitColumns)
    {
        $this->limitColumns = $limitColumns;
    }
}
