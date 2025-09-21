<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Observer;

use Amasty\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\Event\ObserverInterface;

/**
 * observer name: wished_index_invalidate
 * event names:
 *     wishlist_add_product
 */
class WishedIndexInvalidator implements ObserverInterface
{
    /**
     * @var WishedProcessor
     */
    private $indexProcessor;

    /**
     * ViewedIndexInvalidator constructor.
     *
     * @param WishedProcessor $indexProcessor
     */
    public function __construct(WishedProcessor $indexProcessor)
    {
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * Mark Wished indexer as invalid on event process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->indexProcessor->markIndexerAsInvalid();
    }
}
