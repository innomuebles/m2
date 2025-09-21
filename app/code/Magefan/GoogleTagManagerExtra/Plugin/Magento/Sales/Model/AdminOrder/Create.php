<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magento\Sales\Model\AdminOrder;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magento\Backend\Model\Session as BackendSession;

class Create
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
     * @var BackendSession
     */
    private $backendSession;

    /**
     * Create constructor.
     * @param Config $config
     * @param ConfigExtra $configExtra
     * @param BackendSession $backendSession
     */
    public function __construct(
        Config $config,
        ConfigExtra $configExtra,
        BackendSession $backendSession
    ) {
        $this->backendSession = $backendSession;
        $this->config = $config;
        $this->configExtra = $configExtra;
    }

    /**
     * @param \Magento\Sales\Model\AdminOrder\Create $subject
     * @param $result
     * @return mixed
     */
    public function afterCreateOrder(
        \Magento\Sales\Model\AdminOrder\Create $subject,
        $result
    ) {
        $storeId = $result->getStoreId();
        if ($this->config->isEnabled((string)$storeId) && $this->configExtra->isTrackAdminOrdersEnabled((string)$storeId)) {
            $this->backendSession->setMfGtmPurchasedOrderId($result->getId());
        }

        return $result;
    }
}
