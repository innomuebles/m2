<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Observer\Checkout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\ServerTracker\ClientIdProvider;
use Magefan\GoogleTagManagerExtra\Model\ServerTracker\SessionIdProvider;

class ActionPredispatch implements ObserverInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientIdProvider
     */
    private $clientIdProvider;

    /**
     * @var SessionIdProvider
     */
    private $sessionIdProvider;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Config $config
     * @param ClientIdProvider $clientIdProvider
     * @param SessionIdProvider $sessionIdProvider
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $config,
        ClientIdProvider $clientIdProvider,
        SessionIdProvider $sessionIdProvider,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->clientIdProvider = $clientIdProvider;
        $this->sessionIdProvider = $sessionIdProvider;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $fan = $observer->getRequest()->getFullActionName();
        if (in_array($fan, ['checkout_cart_index', 'checkout_index_index'])) {
            if ($quote = $this->checkoutSession->getQuote()) {
                $clientId = $this->clientIdProvider->get();
                if ($quote->getMfGtmClientId() !== $clientId) {
                    $quote->setMfGtmClientId($clientId);
                    try {
                        $this->quoteRepository->save($quote);
                    } catch (\Exception $e) {
                    }
                }

                $sessionId = $this->sessionIdProvider->get();
                if ($quote->getMfGtmSessionId() !== $sessionId) {
                    $quote->setMfGtmSessionId($sessionId);
                    try {
                        $this->quoteRepository->save($quote);
                    } catch (\Exception $e) {
                    }
                }
            }
        } elseif (in_array($fan, ['checkout_onepage_success'])) {
            if ($order = $this->checkoutSession->getLastRealOrder()) {
                $clientId = $this->clientIdProvider->get();
                if ($order->getMfGtmClientId() !== $clientId) {
                    $order->setMfGtmClientId($clientId);
                    try {
                        $this->orderRepository->save($order);
                    } catch (\Exception $e) {
                    }
                }

                $sessionId = $this->sessionIdProvider->get();
                if ($order->getMfGtmSessionId() !== $sessionId) {
                    $order->setMfGtmSessionId($sessionId);
                    try {
                        $this->orderRepository->save($order);
                    } catch (\Exception $e) {
                    }
                }
            }

        }
    }
}
