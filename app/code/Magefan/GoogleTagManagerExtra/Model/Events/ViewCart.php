<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\ViewCartInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Quote\Model\QuoteRepository;
use Magefan\GoogleTagManager\Model\DataLayer\ViewCart as ViewCartDataLayer;

/**
 * Abstract management model
 */
class ViewCart implements ViewCartInterface
{

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ViewCartDataLayer
     */
    protected $viewCart;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param ServerTracker $serverTracker
     * @param Config $config
     * @param ViewCartDataLayer $viewCart
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        ServerTracker $serverTracker,
        Config $config,
        ViewCartDataLayer $viewCart,
        QuoteRepository $quoteRepository
    ) {
        $this->serverTracker = $serverTracker;
        $this->config = $config;
        $this->viewCart = $viewCart;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param string $quoteId
     * @return mixed
     */
    public function execute(string $quoteId)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $quote = $this->quoteRepository->get($quoteId);
            $data = $this->viewCart->get($quote);
            $this->serverTracker->push($data);
        }
    }
}
