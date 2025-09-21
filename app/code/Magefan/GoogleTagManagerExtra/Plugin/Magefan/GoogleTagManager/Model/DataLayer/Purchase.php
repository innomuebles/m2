<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magefan\GoogleTagManager\Model\DataLayer;

use Magefan\GoogleTagManager\Model\DataLayer\Purchase as Subject;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magento\Sales\Model\Order;

class Purchase
{
    /**
     * @var ConfigExtra
     */
    protected $configExtra;

    /**
     * @param ConfigExtra $configExtra
     */
    public function __construct(
        ConfigExtra $configExtra
    ) {
        $this->configExtra = $configExtra;
    }

    /**
     * @param Subject $subject
     * @param $proceed
     * @return mixed
     */
    public function aroundGet(Subject $subject, $proceed, Order $order, string $requester = '')
    {
        if ($this->configExtra->validateOrderStatus($order)) {
            return $proceed($order, $requester);
        }

        return [];
    }
}
