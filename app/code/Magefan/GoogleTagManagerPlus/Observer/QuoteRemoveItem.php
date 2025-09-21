<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Observer;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\RemoveFromCartInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class QuoteRemoveItem implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var RemoveFromCartInterface
     */
    private $removeFromCart;

    /**
     * @var SessionManagerInterface|mixed
     */
    private $sessionManager;

    /**
     * QuoteRemoveItem constructor.
     *
     * @param Config $config
     * @param CheckoutSession $checkoutSession
     * @param RemoveFromCartInterface $removeFromCart
     */
    public function __construct(
        Config $config,
        CheckoutSession $checkoutSession,
        RemoveFromCartInterface $removeFromCart,
        SessionManagerInterface $sessionManager
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->removeFromCart = $removeFromCart;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Set datalayer on remove product from cart
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $quoteItem = $observer->getData('quote_item');
            $this->sessionManager->push(
                $this->checkoutSession,
                $this->removeFromCart->get($quoteItem)
            );
        }
    }
}
