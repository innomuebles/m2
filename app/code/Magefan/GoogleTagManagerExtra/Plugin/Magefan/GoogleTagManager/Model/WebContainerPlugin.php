<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magefan\GoogleTagManager\Model;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManager\Model\WebContainer;
use Magento\Framework\Stdlib\DateTime\DateTime;

class WebContainerPlugin
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Config
     */
    private $config;

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
     * ContainerPlugin constructor.
     *
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        DateTime $dateTime,
        Config $config
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    /**
     * Generate JSON container
     *
     * @param WebContainer $subject
     * @param array $result
     * @param string|null $storeId
     * @return array
     */
    public function afterGenerate(WebContainer $subject, array $result, ?string $storeId = null): array
    {
        $this->timestamp = $this->timestamp ?: (string)$this->dateTime->timestamp();
        $this->accountId = $this->accountId ?: $this->config->getAccountId($storeId);
        $this->containerId = $this->containerId ?: $this->config->getContainerId($storeId);

        $result['containerVersion']['trigger'] = array_merge(
            $result['containerVersion']['trigger'],
            $this->generateTriggers()
        );

        $result['containerVersion']['tag'] = array_merge(
            $result['containerVersion']['tag'],
            $this->generateTags()
        );

        $result['containerVersion']['customTemplate'] = $this->generateCustomTemplates();

        if (isset($result['containerVersion']['trigger'])) {
            foreach ($result['containerVersion']['trigger'] as $key => $trigger) {
                if ($trigger['triggerId'] == 167) {
                    if (isset($trigger['customEventFilter'][0]['parameter'])) {
                        foreach ($trigger['customEventFilter'][0]['parameter'] as $pkey => $parameter) {
                            if (isset($parameter['value']) && false !== strpos($parameter['value'], 'begin_checkout')) {
                                $result['containerVersion']['trigger'][$key]['customEventFilter'][0]['parameter'][$pkey]['value'] .= '|login|sign_up|search|refund';
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get triggers for container
     *
     * @return array
     */
    private function generateTriggers(): array
    {
        $triggers = [];

        $triggerNames = ['Login', 'Sign Up', 'Search', 'Refund'];
        foreach ($triggerNames as $key => $triggerName) {
            $triggers[] = [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'triggerId' => 180 + $key,
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

        return $triggers;
    }

    /**
     * Get tags for container
     *
     * @return array
     */
    private function generateTags(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'tagId' => '99',
                'name' => 'Magefan GA4 - Block Checker',
                'type' => 'cvt_' . $this->containerId . '_100',
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
     * Get custom templates for container
     *
     * @return array
     */
    private function generateCustomTemplates(): array
    {
        return [
            [
                'accountId' => $this->accountId,
                'containerId' => $this->containerId,
                'templateId' => '100',
                'name' => 'Magefan GA4 - Block Checker',
                'fingerprint' => $this->timestamp,
                'templateData' => "___INFO___\n\n{\n  \"displayName\": \"Magefan GA4 - Block Checker\",\n  \"description\": \"Detects if GA4 is blocked\",\n  \"securityGroups\": [],\n  \"id\": \"cvt_temp_public_id\",\n  \"type\": \"TAG\",\n  \"version\": 1,\n  \"brand\": {\n    \"displayName\": \"\",\n    \"id\": \"brand_mf\"\n  },\n  \"containerContexts\": [\n    \"WEB\"\n  ]\n}\n\n\n___TEMPLATE_PARAMETERS___\n\n[]\n\n\n___SANDBOXED_JS_FOR_WEB_TEMPLATE___\n\nconst sendPixel = require('sendPixel');\nconst callInWindow = require('callInWindow');\n\nconst onFailure = function() {\n           \n   callInWindow('dataLayer.push', {\n       'event': 'ga4-blocked'\n   });\n          \n   callInWindow('mfTrackPurchase.GA4Blocked');\n\n};\n        \nconst onSuccess = function() {\n          \n   callInWindow('dataLayer.push', {\n       'event': 'ga4-unblocked'\n   });\n\n          \n   callInWindow('mfTrackPurchase.GA4NotBlocked');\n\n};\n\nsendPixel('https://www.google-analytics.com/collect', onSuccess, onFailure);\n    \n\ndata.gtmOnSuccess();\n\n\n___WEB_PERMISSIONS___\n\n[\n  {\n    \"instance\": {\n      \"key\": {\n        \"publicId\": \"access_globals\",\n        \"versionId\": \"1\"\n      },\n      \"param\": [\n        {\n          \"key\": \"keys\",\n          \"value\": {\n            \"type\": 2,\n            \"listItem\": [\n              {\n                \"type\": 3,\n                \"mapKey\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"key\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"read\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"write\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"execute\"\n                  }\n                ],\n                \"mapValue\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"dataLayer\"\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  }\n                ]\n              },\n              {\n                \"type\": 3,\n                \"mapKey\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"key\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"read\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"write\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"execute\"\n                  }\n                ],\n                \"mapValue\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"dataLayer.push\"\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  }\n                ]\n              },\n              {\n                \"type\": 3,\n                \"mapKey\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"key\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"read\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"write\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"execute\"\n                  }\n                ],\n                \"mapValue\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"mfTrackPurchase\"\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  }\n                ]\n              },\n              {\n                \"type\": 3,\n                \"mapKey\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"key\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"read\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"write\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"execute\"\n                  }\n                ],\n                \"mapValue\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"mfTrackPurchase.GA4Blocked\"\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  }\n                ]\n              },\n              {\n                \"type\": 3,\n                \"mapKey\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"key\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"read\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"write\"\n                  },\n                  {\n                    \"type\": 1,\n                    \"string\": \"execute\"\n                  }\n                ],\n                \"mapValue\": [\n                  {\n                    \"type\": 1,\n                    \"string\": \"mfTrackPurchase.GA4NotBlocked\"\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  },\n                  {\n                    \"type\": 8,\n                    \"boolean\": true\n                  }\n                ]\n              }\n            ]\n          }\n        }\n      ]\n    },\n    \"clientAnnotations\": {\n      \"isEditedByUser\": true\n    },\n    \"isRequired\": true\n  },\n  {\n    \"instance\": {\n      \"key\": {\n        \"publicId\": \"send_pixel\",\n        \"versionId\": \"1\"\n      },\n      \"param\": [\n        {\n          \"key\": \"allowedUrls\",\n          \"value\": {\n            \"type\": 1,\n            \"string\": \"specific\"\n          }\n        },\n        {\n          \"key\": \"urls\",\n          \"value\": {\n            \"type\": 2,\n            \"listItem\": [\n              {\n                \"type\": 1,\n                \"string\": \"https://www.google-analytics.com/collect\"\n              }\n            ]\n          }\n        }\n      ]\n    },\n    \"clientAnnotations\": {\n      \"isEditedByUser\": true\n    },\n    \"isRequired\": true\n  }\n]\n\n\n___TESTS___\n\nscenarios: []\n\n\n___NOTES___\n\nCreated on 21.02.2024, 10:59:35\n\n\n"
            ]
        ];
    }
}
