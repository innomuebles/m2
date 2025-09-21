<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Api\DataLayer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

interface RefundInterface
{
    /**
     * Get GTM datalayer
     *
     * @param Order $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function get(Order $order): array;
}
