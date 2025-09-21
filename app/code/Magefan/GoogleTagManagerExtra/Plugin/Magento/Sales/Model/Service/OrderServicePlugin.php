<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magento\Sales\Model\Service;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\OrderService;

class OrderServicePlugin
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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderServicePlugin constructor.
     *
     * @param Config $config
     * @param BackendSession $backendSession
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $config,
        BackendSession $backendSession,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->backendSession = $backendSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Set datalayer after order cancel in admin
     *
     * @param OrderService $subject
     * @param $id
     * @param $result
     * @return mixed
     */
    public function afterCancel(OrderService $subject, $result, $id)
    {
        if ($result) {
            $order = $this->orderRepository->get($id);
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
