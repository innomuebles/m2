<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Api\DataLayer\Wishlist;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Model\Item;

interface ItemInterface
{
    /**
     * Get wishlist item
     *
     * @param Item $wishlistItem
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(Item $wishlistItem): array;
}
