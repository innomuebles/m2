<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model;

use Magefan\GoogleTagManagerPlus\Model\Config as PlusConfig;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magefan\GoogleTagManager\Api\ContainerInterface;

class ServerContainer implements ContainerInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ConfigExtra
     */
    protected $configExtra;

    /**
     * @var PlusConfig
     */
    private $plusConfig;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $containerId;

    /**
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param Config $config
     * @param \Magefan\GoogleTagManagerExtra\Model\Config $configExtra
     * @param PlusConfig $plusConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        Config $config,
        ConfigExtra $configExtra,
        PlusConfig $plusConfig
    ) {
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->plusConfig = $plusConfig;
    }

    public function generate(?string $storeId = null): array
    {
        $this->timestamp = (string)$this->dateTime->timestamp();
        $this->accountId = $this->configExtra->getGtmServerAccountId($storeId);
        $this->containerId = $this->configExtra->getGtmServerContainerId($storeId);
        $publicId = $this->configExtra->getGtmServerPublicId($storeId);
        $isAnalyticsEnabled = $this->config->isAnalyticsEnabled($storeId);

        return [
            'exportFormatVersion' => 2,
            'exportTime' => $this->dateTime->date('Y-m-d H:i:s'),
            'containerVersion' => [
                'path' => 'accounts/' . $this->accountId . '/containers/' . $this->containerId . '/versions/0',
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'containerVersionId' => '0',
                'container' => [
                    'path' => 'accounts/' . $this->accountId . '/containers/' . $this->containerId,
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    //'name' => '',
                    'publicId' => $publicId,
                    'usageContext' => [
                        'SERVER'
                    ],
                    'fingerprint' => $this->timestamp,
                    'tagManagerUrl' => 'https://tagmanager.google.com/#/container/accounts/' . $this->accountId
                        . '/containers/' . $this->containerId . '/workspaces?apiLink=container',
                    'taggingServerUrls' => [
                        $this->configExtra->getGtmServerUrl()
                    ]
                ],
                'tag' => $this->generateTags($storeId),
                'trigger' => $this->generateTriggers($storeId),
                'variable' => $this->generateVariables($storeId),
                'builtInVariable' => ($isAnalyticsEnabled || $this->isGoogleAdsEnabled($storeId)) ? [
                    [
                        'accountId' => $this->accountId,
                        'containerId' => $this->containerId,
                        'type' => 'EVENT_NAME',
                        'name' => 'Event Name'
                    ]
                ] : [],
                'fingerprint' => $this->timestamp,
                'tagManagerUrl' => 'https://tagmanager.google.com/#/versions/accounts/' . $this->accountId
                    . '/containers/' . $this->containerId . '/versions/0?apiLink=version',
                'client' => $this->generateClients()
            ]
        ];
    }

    /**
     * Get clients for container
     *
     * @return array
     */
    private function generateClients(): array
    {
        return [[
            'accountId' => $this->accountId,
            'containerId' => $this->containerId,
            'clientId' => '1',
            'name' => 'Magefan GA4',
            'type' => 'gaaw_client',
            'parameter' => [
                [
                    'type' => 'TEMPLATE',
                    'key' => 'cookieDomain',
                    'value' => 'auto',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'activateResponseCompression',
                    'value' => 'false',
                ],
                [
                    'type' => 'TEMPLATE',
                    'key' => 'cookieMaxAgeInSec',
                    'value' => '63072000',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'activateGeoResolution',
                    'value' => 'false',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'activateGtagSupport',
                    'value' => 'true',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'activateDependencyServing',
                    'value' => 'true',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'activateDefaultPaths',
                    'value' => 'true',
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'migrateFromJsClientId',
                    'value' => 'false',
                ],
                [
                    'type' => 'TEMPLATE',
                    'key' => 'cookieManagement',
                    'value' => 'server',
                ],
                [
                    'type' => 'TEMPLATE',
                    'key' => 'cookieName',
                    'value' => 'FPID',
                ],
                [
                    'type' => 'LIST',
                    'key' => 'gtagMeasurementIds',
                    'list' => [
                        [
                            'type' => 'MAP',
                            'map' => [
                                [
                                    'type' => 'TEMPLATE',
                                    'key' => 'measurementId',
                                    'value' => '{{Magefan GA4 - Measurement ID}}',
                                ]
                            ]
                        ]
                    ],
                ],
            ],
            'fingerprint' => $this->timestamp
        ]];
    }

    /**
     * Get triggers for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateTriggers(
        ?string $storeId = null
    ): array {
        $triggers = [];

        if ($this->config->isAnalyticsEnabled($storeId) || $this->isGoogleAdsEnabled($storeId)) {
            $triggers = [
                [
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    'triggerId' => '162',
                    'name' => 'Magefan GTM - Configuration',
                    'type' => 'SERVER_PAGEVIEW',
                    'fingerprint' => $this->timestamp
                ],
                [
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    'triggerId' => '167',
                    'name' => 'Magefan GTM - Ecommerce',
                    'type' => 'CUSTOM_EVENT',
                    'customEventFilter' => [
                        [
                            'type' => 'MATCH_REGEX',
                            'parameter' => [
                                [
                                    'type' => 'TEMPLATE',
                                    'key' => 'arg0',
                                    'value' => '{{_event}}'
                                ],
                                [
                                    'type' => 'TEMPLATE',
                                    'key' => 'arg1',
                                    'value' => 'view_item|view_cart|purchase|begin_checkout|view_item_list|select_item|add_to_cart|remove_from_cart|add_payment_info|add_shipping_info|add_to_wishlist|login|sign_up|search|refund|user_engagement|^scroll_depth_.*' // phpcs:ignore
                                ]
                            ]
                        ]
                    ],
                    'fingerprint' => $this->timestamp
                ]
            ];

            $triggerNames = [
                'View Item',
                'View Cart',
                'Purchase',
                'Begin Checkout',
                'View Item List',
                'Add To Cart',
                'Remove From Cart',
                'Add Payment Info',
                'Add Shipping Info',
                'Add To Wishlist',
                'Login',
                'Sign Up',
                'Search',
                'Refund'
            ];

            foreach ($triggerNames as $key => $triggerName) {
                $triggers[] = [
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    'triggerId' => 168 + $key,
                    'name' => 'Magefan GTM - ' . $triggerName,
                    'type' => 'CUSTOM_EVENT',
                    'customEventFilter' => [
                        [
                            'type' => 'EQUALS',
                            'parameter' => [
                                [
                                    'type' => 'TEMPLATE',
                                    'key' => 'arg0',
                                    'value' => '{{_event}}'
                                ],
                                [
                                    'type' => 'TEMPLATE',
                                    'key' => 'arg1',
                                    'value' => strtolower(str_replace(' ', '_', $triggerName))
                                ]
                            ]
                        ]
                    ],
                    'fingerprint' => $this->timestamp
                ];
            }

            $triggers[] = [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'triggerId' => 182,
                'name' => 'Magefan GTM - Scroll Depth',
                'type' => 'CUSTOM_EVENT',
                'customEventFilter' => [
                    [
                        'type' => 'MATCH_REGEX',
                        'parameter' => [
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg0',
                                'value' => '{{_event}}'
                            ],
                            [
                                'type' => 'TEMPLATE',
                                'key' => 'arg1',
                                'value' => '^scroll_depth_.*'
                            ]
                        ]
                    ]
                ],
                'fingerprint' => $this->timestamp
            ];
        }

        return $triggers;
    }

    /**
     * Get tags for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateTags(
        ?string $storeId = null
    ): array {
        $tags = [];

        if ($this->config->isAnalyticsEnabled($storeId)) {

            $tags[] = [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '168',
                'name' => 'Magefan GA4 - Configuration',
                'type' => 'sgtmgaaw',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'redactVisitorIp',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'epToIncludeDropdown',
                        'value' => 'all'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableGoogleSignals',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'upToIncludeDropdown',
                        'value' => 'all'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'measurementId',
                        'value' => '{{Magefan GA4 - Measurement ID}}'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableEuid',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '162'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ];

            $tags[] = [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '169',
                'name' => 'Magefan GA4 - Ecommerce',
                'type' => 'sgtmgaaw',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'redactVisitorIp',
                        'value' => 'false',
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'epToIncludeDropdown',
                        'value' => 'all',
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableGoogleSignals',
                        'value' => 'false',
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'eventName',
                        'value' => '{{Event Name}}',
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'upToIncludeDropdown',
                        'value' => 'all',
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'measurementId',
                        'value' => '{{Magefan GA4 - Measurement ID}}',
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableEuid',
                        'value' => 'false',
                    ],
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '167'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ];


        }

        if ($this->isGoogleAdsEnabled($storeId)) {
            $tags = array_merge($tags, $this->generateGoogleAdsTags($storeId));
        }

        return $tags;
    }

    /**
     * Get google ads tags for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateGoogleAdsTags(
        ?string $storeId = null
    ): array {
        $tags[] = [
            'accountId' => $this->accountId,
            'containerId' => $this->containerId,
            'tagId' => '183',
            'name' => 'Magefan - Conversion Linker',
            'type' => 'sgtmadscl',
            'parameter' => [
                [
                    'type' => 'BOOLEAN',
                    'key' => 'enableLinkerParams',
                    'value' => 'false'
                ],
                [
                    'type' => 'BOOLEAN',
                    'key' => 'enableCookieOverrides',
                    'value' => 'false'
                ]
            ],
            'fingerprint' => $this->timestamp,
            'firingTriggerId' => [
                '162'
            ],
            'tagFiringOption' => 'ONCE_PER_EVENT',
            'monitoringMetadata' => [
                'type' => 'MAP'
            ],
            'consentSettings' => [
                'consentStatus' => 'NOT_SET'
            ]
        ];

        if ($this->plusConfig->isConversionTrackingEnabled($storeId)) {
            $tags = array_merge($tags, $this->getConversionTags($storeId));
        }

        if ($this->plusConfig->isRemarketingEnabled($storeId)) {
            $tags = array_merge($tags, $this->getRemarketingTags($storeId));
        }

        return $tags;
    }

    /**
     * Get conversion tags
     *
     * @param string|null $storeId
     * @return array
     */
    private function getConversionTags(
        ?string $storeId = null
    ): array {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '184',
                'name' => 'Magefan Google Ads - Conversion Tracking',
                'type' => 'sgtmadsct',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableNewCustomerReporting',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableProductReporting',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableEnhancedMatch',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getPurchaseConversionId($storeId)
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionLabel',
                        'value' => $this->plusConfig->getPurchaseConversionLabel($storeId)
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '170'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ]
        ];
    }

    /**
     * Get remarketing tags
     *
     * @param string|null $storeId
     * @return array
     */
    private function getRemarketingTags(
        ?string $storeId = null
    ): array {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '258',
                'name' => 'Magefan Google Ads - Remarketing - Ecommerce',
                'type' => 'sgtmadsremarket',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableDynamicRemarketing',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableCustomParams',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'customParamsFormat',
                        'value' => 'USER_SPECIFIED'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableUserId',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getRemarketingId($storeId)
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionLabel',
                        'value' => $this->plusConfig->getRemarketingLabel($storeId)
                    ],
                    [
                        'type' => 'LIST',
                        'key' => 'customParams',
                        'list' => [
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_pagetype',
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan Query - Page Type}}',
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_prodid',
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan Query - Item ID}}',
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_pcat',
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan Query - Category}}',
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_pname',
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan Query - Pname}}',
                                    ]
                                ]
                            ],
                            [
                                'type' => 'MAP',
                                'map' => [
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'key',
                                        'value' => 'ecomm_totalvalue',
                                    ],
                                    [
                                        'type' => 'TEMPLATE',
                                        'key' => 'value',
                                        'value' => '{{Magefan Query - Value}}',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '167'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '259',
                'name' => 'Magefan Google Ads - Remarketing - Pave View',
                'type' => 'sgtmadsremarket',
                'parameter' => [
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableConversionLinker',
                        'value' => 'true'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableDynamicRemarketing',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'enableUserId',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionId',
                        'value' => $this->plusConfig->getRemarketingId($storeId)
                    ],
                    [
                        'type' => 'BOOLEAN',
                        'key' => 'rdp',
                        'value' => 'false'
                    ],
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'conversionLabel',
                        'value' => $this->plusConfig->getRemarketingLabel($storeId)
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'firingTriggerId' => [
                    '162'
                ],
                'tagFiringOption' => 'ONCE_PER_EVENT',
                'monitoringMetadata' => [
                    'type' => 'MAP'
                ],
                'consentSettings' => [
                    'consentStatus' => 'NOT_SET'
                ]
            ]
        ];
    }

    /**
     * Get variables for container
     *
     * @param string|null $storeId
     * @return array
     */
    private function generateVariables(
        ?string $storeId = null
    ): array {
        $variables = [];

        if ($this->config->isAnalyticsEnabled($storeId)) {
            $variables = [
                [
                    'accountId' => $this->accountId,
                    'containerId' => $this->containerId,
                    'variableId' => '692',
                    'name' => 'Magefan GA4 - Measurement ID',
                    'type' => 'c',
                    'parameter' => [
                        [
                            'type' => 'TEMPLATE',
                            'key' => 'value',
                            'value' => $this->config->getMeasurementId($storeId)
                        ],
                    ],
                    'fingerprint' => $this->timestamp
                ]
            ];

            if ($this->plusConfig->isConversionTrackingEnabled($storeId)) {
                $variables = array_merge($variables, $this->getConversionVariables());
            }

            if ($this->plusConfig->isRemarketingEnabled($storeId)) {
                $variables = array_merge($variables, $this->getRemarketingVariables());
            }
        }

        return $variables;
    }

    /**
     * Get conversion variables
     *
     * @return array
     */
    private function getConversionVariables(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '254',
                'name' => 'Magefan Query - Transaction ID',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecommerce.transaction_id'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '255',
                'name' => 'Magefan Query - Currency',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecommerce.currency'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '256',
                'name' => 'Magefan Query - Customer Email Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_identifier'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '257',
                'name' => 'Magefan Query - Customer Group',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customerGroup'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '258',
                'name' => 'Magefan Query - Customer Telephone Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_telephone'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '259',
                'name' => 'Magefan Query - Customer Firstname Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_firstname'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '260',
                'name' => 'Magefan Query - Customer Lastname Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_lastname'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '261',
                'name' => 'Magefan Query - Customer Street Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_street'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '262',
                'name' => 'Magefan Query - Customer City Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_city'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '263',
                'name' => 'Magefan Query - Customer Country Id Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_country_id'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '264',
                'name' => 'Magefan Query - Customer Postcode Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_postcode'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '265',
                'name' => 'Magefan Query - Customer Region Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_region'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '266',
                'name' => 'Magefan Query - Customer Dob Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_dob'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '267',
                'name' => 'Magefan Query - Customer Gender Hash',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'customer_gender'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ]
        ];
    }

    /**
     * Get remarketing variables
     *
     * @return array
     */
    private function getRemarketingVariables(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '260',
                'name' => 'Magefan Query - Item ID',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecomm_prodid'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '261',
                'name' => 'Magefan Query - Category',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecomm_pcat'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '262',
                'name' => 'Magefan Query - Page Type',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecomm_pagetype'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '263',
                'name' => 'Magefan Query - Pname',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'ecomm_pname'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'variableId' => '264',
                'name' => 'Magefan Query - Value',
                'type' => 'qp',
                'parameter' => [
                    [
                        'type' => 'TEMPLATE',
                        'key' => 'queryParamName',
                        'value' => 'epn.value'
                    ]
                ],
                'fingerprint' => $this->timestamp,
                'formatValue' => (object)[]
            ],
        ];
    }

    /**
     * Check if at least one Google Ads option enabled
     *
     * @param string|null $storeId
     * @return bool
     */
    private function isGoogleAdsEnabled(?string $storeId = null): bool
    {
        return $this->plusConfig->isRemarketingEnabled($storeId) ||
            $this->plusConfig->isConversionTrackingEnabled($storeId);
    }
}
