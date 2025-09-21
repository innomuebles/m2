<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\AddBillingInfoInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magefan\GoogleTagManager\Model\DataLayer\BeginCheckout;

/**
 * Abstract management model
 */
class AddBillingInfo implements AddBillingInfoInterface
{
    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var BeginCheckout
     */
    private $beginCheckout;

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
     * @param string $quoteId
     * @param string $paymentMethod
     * @return mixed
     */
    public function execute(string $quoteId, string $paymentMethod)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id');

            if ($quoteIdMask->getQuoteId()) {
                $quote = $this->cartRepository->get($quoteIdMask->getQuoteId());
            } else {
                $quote = $this->cartRepository->get($quoteId);
            }

            $data = $this->beginCheckout->get($quote);
            if ($data) {
                $data['event'] = 'add_payment_info';
                $data['ecommerce']['payment_type'] = $paymentMethod;
            }
            $this->serverTracker->push($data);
        }
    }
}
