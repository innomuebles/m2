<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Block\ServerTracker;

use Magento\Framework\View\Element\Template;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;

class Js extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param ConfigExtra $configExtra
     * @param ServerTracker $serverTracker
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        ConfigExtra $configExtra,
        ServerTracker $serverTracker,
        array $data = []
    ) {
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->serverTracker = $serverTracker;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->config->isEnabled()
            && (!$this->configExtra->isTrackMissingPurchaseEventsOnly() || (!$this->config->isWebContainerEnabled() || !$this->config->getPublicId()))
            && $this->serverTracker->isEnabled()
        ) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ConfigExtra
     */
    public function getConfigExtra()
    {
        return $this->configExtra;
    }
    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRestMfGtmUrl()
    {
        return rtrim($this->_storeManager->getStore()->getBaseUrl(), '/') . '/rest/V1/mfgtm/dlp';
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId(): int
    {
        return (int)$this->_storeManager->getStore()->getWebsiteId();
    }
}
