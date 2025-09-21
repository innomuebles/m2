<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Observer;

use Amasty\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Magento\Framework\Event\ObserverInterface;

/**
 * observer name: bestsellers_index_invalidate
 * event names:
 *     sales_order_place_after
 *     order_cancel_after
 *     sales_order_state_change_before
 */
class BestsellerIndexInvalidator implements ObserverInterface
{
    /**
     * @var BestsellersProcessor
     */
    private $indexProcessor;

    /**
     * BestsellerIndexInvalidator constructor.
     *
     * @param BestsellersProcessor $indexProcessor
     */
    public function __construct(BestsellersProcessor $indexProcessor)
    {
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * Mark Bestsellers indexer as invalid on event process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->indexProcessor->markIndexerAsInvalid();
    }
}
