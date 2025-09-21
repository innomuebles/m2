<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\Checkout;

use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;

class TrackPurchaseEvent extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Magefan_GoogleTagManagerExtra::checkout/track-purchase-event.phtml';

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ServerTracker
     */
    private $serverTracker;


    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        Config $config,
        ServerTracker $serverTracker,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->serverTracker = $serverTracker;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->getUrl('mfgoogletagmanagerextra/checkout/trackPurchaseEvent');
    }

    /**
     * @return float|string|null
     */
    public function getTransactionId()
    {
        return $this->checkoutSession->getLastRealOrder()->getIncrementId();
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }
}
