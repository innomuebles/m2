<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Api;

/**
 * Interface IndexedMethodInterface
 * @api
 */
interface IndexedMethodInterface extends MethodInterface
{
    /**
     * @return string
     */
    public function getIndexTableName();

    /**
     * Full reindex.
     * Truncate index table, commit insert, revert on error
     *
     * @return void
     * @throws \Exception
     */
    public function reindex();

    /**
     * insert to index table
     *
     * @return $this
     */
    public function doReindex();

    /**
     * Returns Sorting method Table Column name
     * which is using for order collection
     *
     * @return string
     */
    public function getSortingColumnName();
}
