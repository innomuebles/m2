<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer;

use Magefan\GoogleTagManagerPlus\Api\DataLayer\AddToCartInterface;

class AddToCart extends AbstractActionCart implements AddToCartInterface
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'add_to_cart';
    }
}
