<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order;

class Config
{
    /**
     * Server Container config
     */
    public const XML_PATH_ANALYTICS_MEASUREMENT_PROTOCOL_ENABLED = 'mfgoogletagmanager/analytics/measurement_protocol/measurement_enabled';
    public const XML_PATH_ANALYTICS_API_SECRET = 'mfgoogletagmanager/analytics/measurement_protocol/api_secret';
    public const XML_PATH_SST_HEADLESS_STOREFRONT = 'mfgoogletagmanager/server_container/headless_storefront';
    public const XML_PATH_SST_TRACK_MISSING_PURCHASE_EVENTS_ONLY = 'mfgoogletagmanager/server_container/track_missing_purchase_events_only';

    public const XML_PATH_GTM_SERVER_ENABLED = 'mfgoogletagmanager/server_container/enabled';
    public const XML_PATH_GTM_SERVER_PUBLIC_ID = 'mfgoogletagmanager/server_container/public_id';
    public const XML_PATH_GTM_SERVER_ACCOUNT_ID = 'mfgoogletagmanager/server_container/account_id';
    public const XML_PATH_GTM_SERVER_CONTAINER_ID = 'mfgoogletagmanager/server_container/container_id';
    public const XML_PATH_GTM_SERVER_URL = 'mfgoogletagmanager/server_container/tag_server_url';

    public const XML_PATH_GTM_SERVER_PREVIEW_ENABLED = 'mfgoogletagmanager/server_container/preview/enabled';
    public const XML_PATH_GTM_SERVER_PREVIEW_SECRET = 'mfgoogletagmanager/server_container/preview/secret';
    public const XML_PATH_GTM_SERVER_PREVIEW_ALLOW_IPS = 'mfgoogletagmanager/server_container/preview/allow_ips';

    public const XML_PATH_GTM_TRACK_ADMIN_ORDER = 'mfgoogletagmanager/events/purchase/track_admin_orders';

    public const XML_PATH_GTM_TRACK_ALLOWED_ORDER_STATUS = 'mfgoogletagmanager/events/purchase/allowed_order_status';

    public const PURCHASE_EVENT_TRACKED = 'purchase_event_tracked';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isGtmServerEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_GTM_SERVER_ENABLED, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getGtmServerPublicId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GTM_SERVER_PUBLIC_ID, $storeId));
    }

    /**
     * Retrieve GTM account ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getGtmServerAccountId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GTM_SERVER_ACCOUNT_ID, $storeId));
    }

    /**
     * Retrieve GTM container ID
     *
     * @param string|null $storeId
     * @return string
     */
    public function getGtmServerContainerId(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GTM_SERVER_CONTAINER_ID, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getGtmServerUrl(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GTM_SERVER_URL, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function getGtmServerPreviewEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_GTM_SERVER_PREVIEW_ENABLED, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getGtmServerPreviewSecret(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_GTM_SERVER_PREVIEW_SECRET, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return array
     */
    public function getGtmServerPreviewAllowIps(?string $storeId = null): array
    {
        $ips = (string)$this->getConfig(self::XML_PATH_GTM_SERVER_PREVIEW_ALLOW_IPS, $storeId);

        $ips = explode(',', $ips);

        $result = [];
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if ($ip) {
                $result[] = $ip;
            }
        }

        return $result;
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isMeasurementProtocolEnabled(?string $storeId = null): bool
    {
        return $this->getConfig(self::XML_PATH_ANALYTICS_MEASUREMENT_PROTOCOL_ENABLED, $storeId)
            && $this->getApiSecret($storeId) && !$this->isGtmServerEnabled($storeId);
    }

    /**
     * @param string|null $storeId
     * @return string
     */
    public function getApiSecret(?string $storeId = null): string
    {
        return trim((string)$this->getConfig(self::XML_PATH_ANALYTICS_API_SECRET, $storeId));
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isHeadlessStorefront(?string $storeId = null): bool
    {
        return $this->getConfig(self::XML_PATH_SST_HEADLESS_STOREFRONT, $storeId)
            && $this->isGtmServerEnabled($storeId);
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isTrackMissingPurchaseEventsOnly(?string $storeId = null): bool
    {
        return ($this->getConfig(self::XML_PATH_SST_TRACK_MISSING_PURCHASE_EVENTS_ONLY, $storeId)
                && !$this->isHeadlessStorefront($storeId)
                && $this->isGtmServerEnabled($storeId)) || $this->isMeasurementProtocolEnabled();
    }

    /**
     * @param string|null $storeId
     * @return bool
     */
    public function isTrackAdminOrdersEnabled(?string $storeId = null): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_GTM_TRACK_ADMIN_ORDER, $storeId);
    }

    /**
     * @param string|null $storeId
     * @return array
     */
    public function getAllowedOrderStatuses(?string $storeId = null): array
    {
        $orderStatuses = (string)$this->getConfig(self::XML_PATH_GTM_TRACK_ALLOWED_ORDER_STATUS, $storeId);
        if (!$orderStatuses || !($this->isGtmServerEnabled() || $this->isMeasurementProtocolEnabled())) {
            return ['any'];
        }

        return explode(',', $orderStatuses);
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function validateOrderStatus(Order $order): bool
    {
        return (bool)array_intersect([$order->getStatus(), 'any'], $this->getAllowedOrderStatuses((string)$order->getStoreId()));
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param string|null $storeId
     * @return mixed
     */
    public function getConfig(string $path, ?string $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
