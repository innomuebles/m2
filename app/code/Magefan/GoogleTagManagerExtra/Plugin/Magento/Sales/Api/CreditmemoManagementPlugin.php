<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magento\Sales\Api;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Sales\Api\CreditmemoManagementInterface;

class CreditmemoManagementPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BackendSession
     */
    private $backendSession;

    /**
     * CreditmemoManagementPlugin constructor.
     *
     * @param Config $config
     * @param BackendSession $backendSession
     */
    public function __construct(
        Config $config,
        BackendSession $backendSession
    ) {
        $this->config = $config;
        $this->backendSession = $backendSession;
    }

    /**
     * Set datalayer after creditmemo in admin
     *
     * @param CreditmemoManagementInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterRefund(CreditmemoManagementInterface $subject, $result)
    {
        if ($result) {
            $order = $result->getOrder();
            if ($order && $this->config->isEnabled((string)$order->getStoreId())) {
                $orderIds = $this->backendSession->getMfGtmRefundOrderIds();
                if ($orderIds) {
                    $this->backendSession->setMfGtmRefundOrderIds($orderIds . ',' . $order->getEntityId());
                } else {
                    $this->backendSession->setMfGtmRefundOrderIds($order->getEntityId());
                }
            }
        }

        return $result;
    }
}
