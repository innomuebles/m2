<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\OrderBeginInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magefan\GoogleTagManager\Model\DataLayer\BeginCheckout;
use Magefan\GoogleTagManager\Model\Config;

/**
 * Abstract management model
 */
class OrderBegin implements OrderBeginInterface
{

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var BeginCheckout
     */
    protected $beginCheckout;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ServerTracker $serverTracker
     * @param Config $config
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param BeginCheckout $beginCheckout
     */
    public function __construct(
        ServerTracker $serverTracker,
        Config $config,
        CartRepositoryInterface $cartRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        BeginCheckout $beginCheckout
    ) {
        $this->serverTracker = $serverTracker;
        $this->config = $config;
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->beginCheckout = $beginCheckout;
    }

    /**
     * @param string $cartId
     * @return mixed
     */
    public function execute(string $cartId)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {

            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

            if ($quoteIdMask->getQuoteId()) {
                $quote = $this->cartRepository->get($quoteIdMask->getQuoteId());
            } else {
                $quote = $this->cartRepository->get($cartId);
            }

            $data = $this->beginCheckout->get($quote);
            $this->serverTracker->push($data);
        }
    }
}
