<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Api;

/**
 * Interface IndexMethodWrapper
 * @api
 */
interface IndexMethodWrapperInterface
{
    /**
     * @return \Amasty\Sorting\Api\IndexedMethodInterface
     */
    public function getSource();

    /**
     * @return \Magento\Framework\Indexer\ActionInterface
     */
    public function getIndexer();
}
