<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\Adminhtml\DataLayer;

use Magefan\GoogleTagManager\Block\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Context;
use Magefan\GoogleTagManager\Api\DataLayer\PurchaseInterface;

class Purchase extends AbstractDataLayer
{
    /**
     * @var BackendSession
     */
    private $backendSession;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var PurchaseInterface
     */
    private $purchase;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * Purchase constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param BackendSession $backendSession
     * @param OrderFactory $orderFactory
     * @param Emulation $appEmulation
     * @param PurchaseInterface $purchase
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        BackendSession $backendSession,
        OrderFactory $orderFactory,
        Emulation $appEmulation,
        PurchaseInterface $purchase,
        array $data = []
    ) {
        $this->backendSession = $backendSession;
        $this->orderFactory = $orderFactory;
        $this->appEmulation = $appEmulation;
        $this->purchase = $purchase;
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
            return $this->purchase->get($order);
        }

        return [];
    }

    /**
     * @return false|Order
     */
    protected function getOrder()
    {
        if (null === $this->order) {
            $this->order = false;
            if ($orderId = $this->backendSession->getMfGtmPurchasedOrderId()) {
                $order = $this->orderFactory->create();
                $order->load($orderId);
                if ($order->getId()) {
                    $this->order = $order;
                }
            }
        }

        return $this->order;
    }

    /**
     * @return void
     */
    protected function unsetOrder(): void
    {
        $this->backendSession->setMfGtmPurchasedOrderId(null);
    }

    /**
     * Init GTM datalayer
     *
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function _toHtml(): string
    {
        $order = $this->getOrder();
        if (!$order) {
            return '';
        }
        $this->appEmulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        $jsCodeBlock = $this->getLayout()->createBlock(
            \Magefan\GoogleTagManager\Block\GtmCode::class,
            'mfgtm.jscode'
        );
        $jsCodeBlock->setTemplate('Magefan_GoogleTagManager::js_code.phtml')
            ->setArea('frontend');


        $jsServerCodeBlock = $this->getLayout()->createBlock(
            \Magefan\GoogleTagManagerExtra\Block\ServerTracker\Js::class,
            'mfgtmextra.serverside'
        );
        $jsServerCodeBlock->setTemplate('Magefan_GoogleTagManagerExtra::servertracker/js.phtml')
            ->setArea('frontend');

        $html = $jsServerCodeBlock->toHtml() . $jsCodeBlock->toHtml() . parent::_toHtml();

        $this->appEmulation->stopEnvironmentEmulation();

        $this->unsetOrder();
        return $html;
    }
}
