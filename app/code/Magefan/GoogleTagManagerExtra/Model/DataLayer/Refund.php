<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\DataLayer;

use Magefan\GoogleTagManagerExtra\Api\DataLayer\RefundInterface;
use Magefan\GoogleTagManager\Model\DataLayer\AbstractOrder;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GoogleTagManager\Api\DataLayer\Order\ItemInterface;

class Refund extends AbstractOrder implements RefundInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * @var string
     */
    protected $ecommPageType = 'refund';

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'refund';
    }
}
