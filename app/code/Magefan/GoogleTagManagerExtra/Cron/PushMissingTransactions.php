<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Cron;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magefan\GoogleTagManager\Model\DataLayer\Purchase;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManagerExtra\Model\ServerTracker\ClientIdProvider;
use Magefan\GoogleTagManagerExtra\Model\ServerTracker\SessionIdProvider;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class PushMissingTransactions
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var ClientIdProvider
     */
    private $clientIdProvider;

    /**
     * @var SessionIdProvider
     */
    private $sessionIdProvider;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @param DateTime $dateTime
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Config $config
     * @param Purchase $purchase
     * @param ServerTracker $serverTracker
     * @param ClientIdProvider $clientIdProvider
     * @param SessionIdProvider $sessionIdProvider
     * @param CartRepositoryInterface $quoteRepository
     * @param StoreManagerInterface $storeManager
     * @param ConfigExtra $configExtra
     */
    public function __construct(
        DateTime $dateTime,
        OrderCollectionFactory $orderCollectionFactory,
        Config $config,
        Purchase $purchase,
        ServerTracker $serverTracker,
        ClientIdProvider $clientIdProvider,
        SessionIdProvider $sessionIdProvider,
        CartRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager,
        ConfigExtra $configExtra
    ) {
        $this->dateTime = $dateTime;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->config = $config;
        $this->purchase = $purchase;
        $this->serverTracker = $serverTracker;
        $this->clientIdProvider = $clientIdProvider;
        $this->sessionIdProvider = $sessionIdProvider;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->configExtra = $configExtra;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $dateFromShift = 3600;

            $allowedOrderStatuses = $this->configExtra->getAllowedOrderStatuses();
            $specificOrderStatus = count($allowedOrderStatuses)
                && !in_array('any', $allowedOrderStatuses);

            if ($specificOrderStatus) {
                $dateFromShift = 2 * 24 * 60 * 60;
            }

            $dateFrom = $this->dateTime->gmtDate(null, time() - $dateFromShift);
            $dateTo = $this->dateTime->gmtDate(null, time() - 900);

            $trackedOrders = $this->orderCollectionFactory->create()
                ->addFieldToFilter('mfgt.requester', ['eq' => ConfigExtra::PURCHASE_EVENT_TRACKED])
                ->addFieldToFilter('created_at', ['gteq' => $dateFrom])
                ->addFieldToFilter('created_at', ['lteq' => $dateTo]);

            $trackedOrders->getSelect()->joinLeft(
                ['mfgt' => $trackedOrders->getTable('magefan_gtm_transaction')],
                'main_table.increment_id = mfgt.transaction_id'
            )->group('main_table.entity_id');
            $trackedOrdersIds = $trackedOrders->getAllIds();

            $orders = $this->orderCollectionFactory->create()
                ->addFieldToFilter('created_at', ['gteq' => $dateFrom])
                ->addFieldToFilter('created_at', ['lteq' => $dateTo]);

            if ($specificOrderStatus) {
                $orders->addFieldToFilter('status', ['in' => $allowedOrderStatuses]);
            }

            foreach ($orders as $order) {
                if (in_array($order->getId(), $trackedOrdersIds)) {
                    continue;
                }

                $mfGtmClientId = $order->getMfGtmClientId();
                if (!$mfGtmClientId) {
                    $quoteId = (int)$order->getQuoteId();
                    if ($quoteId) {
                        try {
                            $quote = $this->quoteRepository->get($quoteId);
                            $mfGtmClientId = $quote->getMfGtmClientId();
                        } catch (NoSuchEntityException $e) {

                        }
                    }
                }

                $mfGtmSessionId = $order->getMfGtmSessionId();
                if (!$mfGtmSessionId) {
                    $quoteId = (int)$order->getQuoteId();
                    if ($quoteId) {
                        try {
                            $quote = $this->quoteRepository->get($quoteId);
                            $mfGtmSessionId = $quote->getMfGtmSessionId();
                        } catch (NoSuchEntityException $e) {

                        }
                    }
                }

                if ($mfGtmClientId) {
                    $this->clientIdProvider->set($mfGtmClientId);
                }

                if ($mfGtmSessionId) {
                    $this->sessionIdProvider->set($mfGtmSessionId);
                }

                $data = $this->purchase->get($order, ServerTracker::REQUESTER_TYPE);
                if (!$data) {
                    continue;
                }
                $data['additional_data']['ip'] = $order->getRemoteIp();

                try {
                    $store = $this->storeManager->getStore($order->getStoreId());
                    $data['dl'] = rtrim($store->getBaseUrl(), '/') . '/checkout/onepage/success/';
                } catch (NoSuchEntityException $e) {

                }

                $this->serverTracker->push($data);
            }
        }
    }
}
