<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\Adminhtml\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Block\GtmCode;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Magefan\GoogleTagManagerExtra\Api\DataLayer\RefundInterface;

class Refund extends AbstractDataLayer
{
    /**
     * @var BackendSession
     */
    private $backendSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * @var RefundInterface
     */
    private $refund;

    /**
     * @var array
     */
    private $order = [];

    /**
     * Refund constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param BackendSession $backendSession
     * @param OrderRepositoryInterface $orderRepository
     * @param Emulation $emulation
     * @param RefundInterface $refund
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        BackendSession $backendSession,
        OrderRepositoryInterface $orderRepository,
        Emulation $emulation,
        RefundInterface $refund,
        array $data = []
    ) {
        $this->backendSession = $backendSession;
        $this->orderRepository = $orderRepository;
        $this->emulation = $emulation;
        $this->refund = $refund;
        parent::__construct($context, $config, $data);
    }

    /**
     * Get GTM datalayer for checkout success page
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getDataLayer(): array
    {
        if ($order = $this->getOrder()) {
            return $this->refund->get($order);
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml(): string
    {
        $orderIds = trim($this->backendSession->getMfGtmRefundOrderIds() ?: '');
        if (!$orderIds) {
            $this->unsetOrder();
            return '';
        }

        $html = '';
        $firstGtmPublicId = null;
        $firstOrderStoreId = null;

        $orderIds = explode(',', $orderIds);
        foreach ($orderIds as $orderId) {
            $this->setOrderId($orderId);
            $order = $this->getOrder();
            if (!$order) {
                continue;
            }
            $orderGtmPublicId = $this->config->getPublicId($order->getStoreId());
            if (!$this->config->isWebContainerEnabled($order->getStoreId()) || !$firstGtmPublicId) {
                $firstGtmPublicId = $orderGtmPublicId;
                $firstOrderStoreId = $order->getStoreId();
            } elseif ($firstGtmPublicId !== $orderGtmPublicId) {
                $this->unsetOrder();
                return '';
            }

            $html .= parent::_toHtml();
        }

        if (!$firstOrderStoreId) {
            $this->unsetOrder();
            return '';
        }

        $this->emulation->startEnvironmentEmulation($firstOrderStoreId, Area::AREA_FRONTEND, true);

        $jsCodeBlock = $this->getLayout()->createBlock(GtmCode::class, 'mfgtm.jscode');
        $jsCodeBlock
            ->setTemplate('Magefan_GoogleTagManager::js_code.phtml')
            ->setArea('frontend');

        $html = $jsCodeBlock->toHtml() . $html;

        $this->emulation->stopEnvironmentEmulation();

        $this->unsetOrder();

        return $html;
    }

    /**
     * Get the order
     *
     * @return bool|Order
     */
    private function getOrder()
    {
        $orderId = $this->getOrderId();
        if (!$orderId) {
            return false;
        }

        if (!isset($this->order[$orderId])) {
            $this->order[$orderId] = false;
            $order = $this->orderRepository->get($orderId);
            if ($order->getId()) {
                $this->order[$orderId] = $order;
            }
        }

        return $this->order[$orderId];
    }

    /**
     * Unset the order from backend session
     *
     * @return void
     */
    private function unsetOrder(): void
    {
        $this->backendSession->setMfGtmRefundOrderIds(null);
    }
}
