<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magento\Framework\Exception\NoSuchEntityException;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\DataLayer\Purchase;

class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @param Config $config
     * @param ConfigExtra $configExtra
     * @param ServerTracker $serverTracker
     * @param Purchase $purchase
     */
    public function __construct(
        Config $config,
        ConfigExtra $configExtra,
        ServerTracker $serverTracker,
        Purchase $purchase
    ) {
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->serverTracker = $serverTracker;
        $this->purchase = $purchase;
    }

    /**
     * Set datalayer after add product to cart
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getData('order');
        if ($order && $this->configExtra->validateOrderStatus($order)) {
            $storeId = (string)$order->getStoreId();
            if ($this->config->isEnabled($storeId)
                && $this->configExtra->isHeadlessStorefront($storeId)
                && $this->serverTracker->isEnabled()
            ) {
                $data = $this->purchase->get($order, ServerTracker::REQUESTER_TYPE);
                $this->serverTracker->push($data);
            }
        }
    }
}
