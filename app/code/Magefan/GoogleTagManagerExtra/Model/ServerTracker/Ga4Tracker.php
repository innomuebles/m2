<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\ServerTracker;

use Magefan\GoogleTagManagerExtra\Api\ServerTracker\TrackerInterface;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;

/**
 * Class ServerTracker
 */
class Ga4Tracker implements TrackerInterface
{
    /**
     * @var ClientIdProvider
     */
    private $clientId;

    /**
     * @var SessionIdProvider
     */
    private $sessionId;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @param ClientIdProvider $clientId
     * @param Config $config
     * @param ConfigExtra $configExtra
     */
    public function __construct(
        ClientIdProvider $clientId,
        Config $config,
        ConfigExtra $configExtra,
        SessionIdProvider $sessionId
    ) {
        $this->clientId = $clientId;
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->sessionId = $sessionId;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function push(array $data): bool
    {
        $this->sendRequest(
            $this->prepareData($data)
        );

        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return  $this->configExtra->isMeasurementProtocolEnabled()
            && !$this->configExtra->isGtmServerEnabled();
    }

    /**
     * @param array $data
     */
    private function sendRequest(array $data): void
    {
        $propertyId = $this->config->getMeasurementId();
        $apiSecret = $this->configExtra->getApiSecret() ;
        $url = 'https://www.google-analytics.com/mp/collect?measurement_id=' . $propertyId . '&api_secret=' . $apiSecret ;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        if (isset($data['event'])) {
            $data = [$data];
        }

        $events = [];
        foreach ($data as $key => $item) {
            if (array_key_exists('event', $item) && array_key_exists('ecommerce', $item)) {
                $params = $item['ecommerce'] ?? '';

                if (is_array($params)) {
                    $params['engagement_time_msec'] = 1;
                    $params['session_id'] = (int)$this->sessionId->get();
                }

                $events[] = [
                    'name' => $item['event'],
                    'params' => $params
                ];
            }
        }

        $result = [
            'client_id' => $this->clientId->get(),
            'non_personalized_ads' => true,
            'events' => $events,
            'timestamp_micros' => (int)(microtime(true) * 1_000_000)
        ];

        return $result;
    }
}
