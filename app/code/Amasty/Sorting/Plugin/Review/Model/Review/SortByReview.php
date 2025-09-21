<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Review\Model\Review;

use Amasty\Sorting\Api\ProductReviewLinkProcessorInterface;
use Amasty\Sorting\Helper\Data;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Processor as FulltextIndexer;
use Magento\Review\Model\Review;

class SortByReview
{
    /**
     * @var ProductReviewLinkProcessorInterface
     */
    private $productReviewLinkProcessor;

    /**
     * @var FulltextIndexer
     */
    private $fulltextIndexer;

    /**
     * @var Data
     */
    private $moduleHelper;

    public function __construct(
        ProductReviewLinkProcessorInterface $productReviewLinkProcessor,
        FulltextIndexer $fulltextIndexer,
        Data $moduleHelper
    ) {
        $this->productReviewLinkProcessor = $productReviewLinkProcessor;
        $this->fulltextIndexer = $fulltextIndexer;
        $this->moduleHelper = $moduleHelper;
    }

    public function afterAggregate(Review $subject, Review $review): Review
    {
        if ($review->getEntityId() === $review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)) {
            $productId = (int) $review->getEntityPkValue();
            $this->productReviewLinkProcessor->create($productId, (int) $review->getId());

            if ($this->moduleHelper->isElasticSort(true)) {
                $this->fulltextIndexer->reindexRow($productId, false);
            }
        }

        return $review;
    }
}
