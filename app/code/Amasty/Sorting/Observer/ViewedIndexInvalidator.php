<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Observer;

use Amasty\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Magento\Framework\Event\ObserverInterface;

/**
 * observer name: most_viewed_index_invalidate
 * event names:
 *     catalog_controller_product_view
 */
class ViewedIndexInvalidator implements ObserverInterface
{
    /**
     * @var MostViewedProcessor
     */
    private $indexProcessor;

    /**
     * ViewedIndexInvalidator constructor.
     *
     * @param MostViewedProcessor $indexProcessor
     */
    public function __construct(MostViewedProcessor $indexProcessor)
    {
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * Mark MostViewed indexer as invalid on event process
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->indexProcessor->markIndexerAsInvalid();
    }
}
