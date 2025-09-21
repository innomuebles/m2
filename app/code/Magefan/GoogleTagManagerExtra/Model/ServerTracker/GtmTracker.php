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
use Magefan\GeoIp\Api\IpToCountryRepositoryInterface;
use Magefan\GeoIp\Api\IpToRegionRepositoryInterface;
use Magefan\GoogleTagManagerExtra\Model\SessionRepository;
use Magento\Framework\App\RequestInterface;

class GtmTracker implements TrackerInterface
{
    /**
     * @var ClientIdProvider
     */
    private $clientId;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @var IpToCountryRepositoryInterface
     */
    private $ipToCountryRepository;

    /**
     * @var IpToRegionRepositoryInterface
     */
    private $ipToRegionRepository;

    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    private $consent;

    /**
     * @param ClientIdProvider $clientId
     * @param Config $config
     * @param ConfigExtra $configExtra
     * @param IpToCountryRepositoryInterface $ipToCountryRepository
     * @param IpToRegionRepositoryInterface $ipToRegionRepository
     * @param SessionRepository $sessionRepository
     * @param RequestInterface $request
     */
    public function __construct(
        ClientIdProvider $clientId,
        Config $config,
        ConfigExtra $configExtra,
        IpToCountryRepositoryInterface $ipToCountryRepository,
        IpToRegionRepositoryInterface $ipToRegionRepository,
        SessionRepository $sessionRepository,
        RequestInterface $request
    ) {
        $this->clientId = $clientId;
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->ipToCountryRepository = $ipToCountryRepository;
        $this->ipToRegionRepository = $ipToRegionRepository;
        $this->sessionRepository = $sessionRepository;
        $this->request = $request;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function push(array $data): bool
    {
        if (isset($data['event'])) {
            $data = [$data];
        }

        foreach ($data as $item) {
            $this->setConsent($item['consent'] ?? null);

            $item = $this->prepareData($item);
            if ($item) {
                $this->sendRequest($item);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configExtra->isGtmServerEnabled();
    }

    /**
     * @param array $data
     */
    private function sendRequest(array $data): void
    {
        $url = trim($this->configExtra->getGtmServerUrl(), '/') . '/g/collect' . '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986) . '&richsstsse';


        $ch = curl_init();
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . 0,
            'User-Agent: ' . $this->getUserAgent()
        ];
        if ($this->configExtra->getGtmServerPreviewEnabled()) {
            if ($serverPreview = $this->configExtra->getGtmServerPreviewSecret()) {
                $allowedIps = $this->configExtra->getGtmServerPreviewAllowIps();
                if (!$allowedIps || in_array($this->ipToCountryRepository->getRemoteAddress(), $allowedIps)) {
                    $headers[] = 'x-gtm-server-preview: ' . $serverPreview;
                }
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($response) {
            $this->runAdditionalRequestsToAdds((string)$response);
        }

        curl_close($ch);
    }

    /**
     * @param string $response
     * @return void
     */
    private function runAdditionalRequestsToAdds(string $response): void
    {
        //run https://googleads.g.doubleclick.net for google ads remarketing
        //run https://googleads.g.doubleclick.net/pagead/viewthroughconversion for google ads conversions
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            if (strpos($line, 'data: ') === 0) {
                $jsonPart = substr($line, 6);
                $decodedJson = json_decode($jsonPart, true);
                if (isset($decodedJson['send_pixel'][0])) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_URL, $decodedJson['send_pixel'][0]);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: ' .  $this->getUserAgent()]);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }
        }
    }

    /**
     * @return string
     */
    private function getUserAgent(): string
    {
        if (!$this->getConsent()) {
            return '';
        }

        return (string)$this->request->getHeader('User-Agent');
    }

    /**
     * @param array $item
     * @return array
     */
    private function prepareData(array $item): array
    {
        if (!isset($item['event'])) {
            return [];
        }

        if (!$this->getConsent()) {
            $item = $this->removeConsentSensitiveData($item);
        }

        $preparedData = [];
        $preparedData = $this->addAuthData($preparedData);
        $session = $this->sessionRepository->get($this->clientId->get());

        $preparedData['gcs'] = 'G111'; //consent is accepted or G100 unaccepted
        $preparedData['gcd'] = '13r3r3r3r5l1';
        $preparedData['dma'] = '1';
        $preparedData['npa'] = '1';

        $preparedData['cid'] = $session->getClientId();
        $preparedData['sid'] = $session->getSessionId();
        $preparedData['sct'] = $session->getSessionCount();
        $preparedData['seg'] = 1;

        $needSaveSession = false;

        $queryParams = $this->getQueryParamsFromUrl($item['dl'] ?? '');
        if (isset($queryParams['gad_source']) && isset($queryParams['gclid'])) {
            $session->setSessionData(['gad_source' => $queryParams['gad_source'], 'gclid' => $queryParams['gclid']]);
            $needSaveSession = true;
        }

        $sourceFields = ['dr' => 'dr', 'utm_source' => 'cs', 'utm_medium' => 'cm', 'utm_campaign' => 'cn'];

        if ('page_view' == $item['event'] || 'user_engagement' == $item['event']) {
            if ($session->getIsFirstVisit()) {
                $preparedData['_fv'] = 1;
                $preparedData['seg'] = 0;

                foreach ($sourceFields as $sourceWebField => $sourceServerField) {
                    if (isset($item[$sourceWebField])) {
                        $session->setSessionData([$sourceServerField => $item[$sourceWebField]]);
                    }
                }
            }

            if ($session->getNewSession()) {
                $preparedData['_ss'] = 1;
            }

            if (isset($session->getSessionData()['page_view_previous_timestamp']) && 'user_engagement' == $item['event']) {
                $pageViewPreviousTimeStamp = $session->getSessionData()['page_view_previous_timestamp'];
                $_et = (time() - $pageViewPreviousTimeStamp) * 1000;
                if ($_et >= 30 * 60 * 1000) {
                    return [];
                }
                $preparedData['_et'] = $_et;
            }

            $session->setSessionData(['page_view_previous_timestamp' => time()]);
            $needSaveSession = true;
        }

        if ('purchase' == $item['event'] && isset($session->getSessionData()['source'])) {
            $preparedData['utm_source'] = $session->getSessionData()['source'];
        }

        $sessionData = $session->getSessionData();
        foreach ($sourceFields as $sourceServerField) {
            if (isset($sessionData[$sourceServerField])) {
                $preparedData[$sourceServerField] = $sessionData[$sourceServerField];
            }
        }

        if ($needSaveSession) {
            $this->sessionRepository->save($session);
        }

        $ip = $item['additional_data']['ip'] ?? $this->ipToCountryRepository->getRemoteAddress();

        if ($ip) {
            $preparedData['ur'] = $this->ipToCountryRepository->getCountryCode($ip)
                . '-' . $this->ipToRegionRepository->getRegionCode($ip);

            $preparedData['_uip'] = $ip;
            if ($preparedData['_uip'] && is_string($preparedData['_uip'])) {
                /* Anonimize IP */
                if (strpos($preparedData['_uip'], '.') !== false) {
                    $delimiter = '.';
                } else {
                    $delimiter = ':';
                }

                $ipd = explode($delimiter, $preparedData['_uip']);
                if (count($ipd) > 1) {
                    $ipd[count($ipd) - 1] = ($delimiter === '.') ? '0' : '0000';
                }
                $preparedData['_uip'] = implode($delimiter, $ipd);
                /* End Anonimize IP */
            }
        }

        foreach ([
            'dl',
            'dt',
            'uafvl',
            'ul',
            'sr',
            'is_virtual',
            'shipping_description',
            'customer_is_guest',
            'customer_identifier',
            'customerGroup',
            'ecomm_pagetype',
            'dr',
            'customer_city',
            'customer_country_id',
            'customer_dob',
            'customer_email',
            'customer_firstname',
            'customer_lastname',
            'customer_gender',
            'customer_postcode',
            'customer_region',
            'customer_telephone'
            ] as $param) {
            if (isset($item[$param])) {
                $preparedData[$param] = $item[$param];
            }
        }

        $sessionData = $session->getSessionData();
        if (isset($preparedData['dl'])
            && (false === strpos($preparedData['dl'], 'gad_source='))
            && (false === strpos($preparedData['dl'], 'gclid='))
            && isset($sessionData['gad_source'])
            && isset($sessionData['gclid'])) {

            $paramsPrefix = (false !== strpos($preparedData['dl'], '?')) ? '&' : '?';
            $preparedData['dl'] .= $paramsPrefix . 'gad_source=' . $sessionData['gad_source']
                . '&gclid=' . $sessionData['gclid'];
        }

        $prC = 1;

        $preparedData['en'] = $item['event'];

        if (isset($item['ecommerce'])) {
            foreach ($item['ecommerce'] as $epKey => $epItem) {
                if (is_string($epItem)) {
                    $preparedData['ep.' . $epKey] = $epItem;
                } elseif (is_numeric($epItem)) {
                    $preparedData['epn.' . $epKey] = $epItem;
                } elseif (is_array($epItem)) {
                    foreach ($epItem as $epItemData) {
                        $preparedData['pr' . $prC] = $this->mapKeysToRight(http_build_query($epItemData));
                        $prC++;
                    }
                }
            }
        }

        if (isset($item['google_tag_params'])) {
            foreach ($item['google_tag_params'] as $key => $value) {
                $preparedData[$key] = $value;
            }
        }

        return $preparedData;
    }

    /**
     * @param array $item
     * @return array
     */
    private function removeConsentSensitiveData(array $item): array
    {
        if (isset($item['dl'])) {
            $parsedUrl = parse_url($item['dl']);
            parse_str($parsedUrl['query'] ?? '', $queryParams);
            $filteredParams = array_intersect_key($queryParams, array_flip(['gclid', 'gad_source']));
            $item['dl'] = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            if (!empty($parsedUrl['path'])) {
                $item['dl'] .= $parsedUrl['path'];
            }
            if (!empty($filteredParams)) {
                $item['dl'] .= '?' . http_build_query($filteredParams);
            }
        }
        unset($item['dr']);
        unset($item['sr']);
        unset($item['uafvl']);

        return $item;
    }

    /**
     * @param string $url
     * @return array
     */
    private function getQueryParamsFromUrl(string $url): array
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            return $queryParams;
        } else {
            return [];
        }
    }

    /**
     * @param string $str
     * @return string
     */
    private function mapKeysToRight(string $str): string
    {
        $map = [
            'item_id' => 'id',
            '&' => '~',
            'item_name' => 'nm',
            'price' => 'pr',
            '=' => '',
            'item_brand' => 'br',
            'discount' => 'ds',
            'quantity' => 'qt',
            'coupon_code' => 'k0coupon_code~v0'
        ];

        foreach ($map as $wrong => $right) {
            $str = str_replace($wrong, $right, $str);
        }

        return $str;
    }

    /**
     * @param array $data
     * @return array
     */
    private function addAuthData(array $data): array
    {
        $data['v'] = 2;
        $data['tid'] = $this->config->getMeasurementId();

        return $data;
    }

    /**
     * @param $value
     * @return $this
     */
    private function setConsent($value)
    {
        $this->consent = $value;
        return $this;
    }

    /**
     * @return bool
     */
    private function getConsent(): bool
    {
        if (!$this->config->isProtectCustomerDataEnabled()) {
            return true;
        }
        return (bool)$this->consent;
    }
}
