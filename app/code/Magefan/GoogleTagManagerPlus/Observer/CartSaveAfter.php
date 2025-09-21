<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Model\AddToCartRegistry;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\AddToCartInterface;
use Magento\Framework\Event\Observer;

class CartSaveAfter implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var AddToCartRegistry
     */
    private $addToCartRegistry;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var AddToCartInterface
     */
    private $addToCart;

    /**
     * @param Config $config
     * @param AddToCartRegistry $addToCartRegistry
     * @param SessionManagerInterface $sessionManager
     * @param CheckoutSession $checkoutSession
     * @param AddToCartInterface $addToCart
     */
    public function __construct(
        Config $config,
        AddToCartRegistry $addToCartRegistry,
        SessionManagerInterface $sessionManager,
        CheckoutSession $checkoutSession,
        AddToCartInterface $addToCart
    ) {
        $this->config = $config;
        $this->addToCartRegistry = $addToCartRegistry;
        $this->sessionManager = $sessionManager;
        $this->checkoutSession = $checkoutSession;
        $this->addToCart = $addToCart;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            foreach ($this->addToCartRegistry->getItems() as $quoteItem) {
                $this->sessionManager->push(
                    $this->checkoutSession,
                    $this->addToCart->get($quoteItem)
                );
            }

            $this->addToCartRegistry->unsetItems();
        }
    }
}
