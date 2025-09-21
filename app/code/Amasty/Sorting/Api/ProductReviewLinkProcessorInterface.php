<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Api;

/**
 * @api
 */
interface ProductReviewLinkProcessorInterface
{
    /**
     * @param int $productId
     * @param int $reviewId
     */
    public function create(int $productId, int $reviewId): void;

    /**
     * @param int $productId
     * @param int $reviewId
     */
    public function remove(int $productId, int $reviewId): void;
}
