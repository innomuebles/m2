<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model;

use Amasty\Sorting\Api\IndexMethodWrapperInterface;
use Amasty\Sorting\Api\IndexedMethodInterface;
use Amasty\Sorting\Model\Indexer\AbstractIndexer;

/**
 * This Class used for DI VirtualType
 */
class IndexMethodWrapper implements IndexMethodWrapperInterface
{
    /**
     * @var IndexedMethodInterface
     */
    private $source;

    /**
     * @var AbstractIndexer
     */
    private $indexer;

    /**
     * IndexMethodWrapper constructor.
     *
     * @param IndexedMethodInterface $source
     * @param AbstractIndexer        $indexer
     */
    public function __construct(
        IndexedMethodInterface $source,
        AbstractIndexer $indexer
    ) {
        $this->source = $source;
        $this->indexer = $indexer;
    }

    /**
     * @return IndexedMethodInterface
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return AbstractIndexer
     */
    public function getIndexer()
    {
        return $this->indexer;
    }
}
