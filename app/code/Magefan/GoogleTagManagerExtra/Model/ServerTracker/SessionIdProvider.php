<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\ServerTracker;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Customer\Model\Session;

/**
 *
 * @deprecated
 */
class SessionIdProvider
{
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param Config $config
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Session $session
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        Config $config,
        CookieMetadataFactory $cookieMetadataFactory,
        Session $session
    ) {
        $this->cookieManager = $cookieManager;
        $this->config = $config;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        if (($result = $this->getFromCookies()) || ($result = $this->session->getMfSessionId())) {
            $this->set($result);
            return $result;
        } else {
            $result = $this->generate();
            $this->set($result);
            return $result;
        }
    }

    /**
     * @param string $value
     * @return SessionIdProvider
     */
    public function set(string $value): SessionIdProvider
    {
        $this->session->setMfSessionId($value);
        return $this;
    }

    /**
     * @return string
     */
    private function generate(): string
    {
        return (string)time();
    }

    /**
     * @return string
     */
    private function getFromCookies(): string
    {
        $raw = $this->cookieManager->getCookie('_ga_' . str_replace('G-', '', $this->config->getMeasurementId()));
        if (!$raw) {
            return '';
        } else {
            if (preg_match('/s(\d{10})/', $raw, $matches)) {
                return $matches[1];
            } else {
                return '';
            }
        }
    }
}
