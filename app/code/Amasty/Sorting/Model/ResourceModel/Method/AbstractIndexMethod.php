<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Api\IndexedMethodInterface;
use Magento\Framework\DB\Query\BatchIteratorInterface;
use Magento\Framework\DB\Query\Generator as BatchQueryGenerator;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotDeleteException;

abstract class AbstractIndexMethod extends AbstractMethod implements IndexedMethodInterface
{
    protected function _construct()
    {
        // product_id can be not unique
        $this->_init($this->getIndexTableName(), 'product_id');
    }

    abstract public function doReindex();

    /**
     * @return $this
     * @throws CouldNotDeleteException
     */
    public function beforeReindex()
    {
        try {
            if ($this->getMethodCode() != 'rating_summary') {
                $this->getConnection()->truncateTable($this->getMainTable());
            }
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Error while clear index amasty sorting method: '), $e);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reindex()
    {
        if ($this->getConnection()->getTransactionLevel() == 0) {
            $this->beforeReindex();

            try {
                if ($this->indexConnection) {
                    $this->doReindex();
                }
            } catch (\Exception $e) {
                $this->logger->critical(
                    $e,
                    ['method_code' => $this->getMethodCode()]
                );
                throw $e;
            }

            $this->afterReindex();
        }
    }

    /**
     * @return $this
     */
    public function afterReindex()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTable()
    {
        return $this->getTable($this->getIndexTableName());
    }

    /**
     * @return string
     */
    public function getIndexTableName()
    {
        return 'amasty_sorting_' . $this->getMethodCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return $this->getMethodCode();
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->getMethodCode();
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction)
    {
        try {
            $collection->joinField(
                $this->getAlias(),        // alias
                $this->getIndexTableName(),    // table
                $this->getSortingColumnName(), // field
                'product_id = entity_id',      // bind
                ['store_id' => $this->storeManager->getStore()->getId()], // conditions
                'left'                         // join type
            );
        } catch (LocalizedException $e) {
            // A joined field with this alias is already declared.
            $this->logger->warning(
                'Failed on join table for amasty sorting method: ' . $e->getMessage(),
                ['method_code' => $this->getMethodCode()]
            );
        } catch (\Exception $e) {
            $this->logger->critical($e, ['method_code' => $this->getMethodCode()]);
        }

        return $this;
    }

    protected function getBatchSelectIterator(string $rangeField, Select $select): BatchIteratorInterface
    {
        return $this->getBatchQueryGenerator()->generate(
            $rangeField,
            $select,
            $this->getBatchSize(),
            BatchIteratorInterface::NON_UNIQUE_FIELD_ITERATOR
        );
    }

    private function getBatchSize(): int
    {
        return (int) ($this->getAdditionalData('batchSize') ?? 1000);
    }

    protected function getBatchQueryGenerator(): BatchQueryGenerator
    {
        return $this->getAdditionalData('batchQueryGenerator');
    }
}
