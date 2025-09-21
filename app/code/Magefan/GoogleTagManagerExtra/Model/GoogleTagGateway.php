<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model;

/*
 * Vanilla PHP Class for
 * https://developers.google.com/tag-platform/tag-manager/gateway/setup-guide?setup=manual#other
*/
class GoogleTagGateway
{
    const PATH = '/mfgtmproxy';

    /**
     * @var string
     */
    private static $ip;

    /**
     * @var string
     */
    private static $countryCode;

    /**
     * @var String
     */
    private static $regionCode;

    /**
     * @return bool
     */
    public static function isProxyRequest()
    {
        return (
            php_sapi_name() !== 'cli' &&
            isset($_SERVER['REQUEST_URI']) &&
            strpos($_SERVER['REQUEST_URI'], self::PATH) !== false
        );
    }

    /**
     * @return void
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public static function execute()
    {
        /*
        $queryString = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/mfgtmproxy/') + strlen('/mfgtmproxy/'));
        $queryString = '/' . ltrim($queryString, '/');
        */
        $queryString = $_SERVER['REQUEST_URI'];
        $targetUrl = 'https://' . self::getTagId() . '.fps.goog';
        $forwardUrl = $targetUrl . $queryString;



        $ch = curl_init($forwardUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Set cookies
        if ($cookiesString = self::getCookieString()) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookiesString);
        }

        /** Form headers for the request */
        $headers = [
            'Host: ' . self::getTagId() . '.fps.goog',
        ];

        $headers[] = 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'];

        if ($countryCode = self::getCountryCode()) {
            $headers[] = 'X-Forwarded-Country: ' . $countryCode;

            if ($regionCode = self::getRegionCode()) {
                $headers[] = 'X-Forwarded-Region: ' . $countryCode . '-' . $regionCode;
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        /** Get an answer */
        $response = curl_exec($ch);

        if ($response === false) {
            http_response_code(502);
            echo "Bad Gateway";
            exit;
        }


        /** Separate the headings from the body */
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers_raw = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        /** We set headers in response to the client */
        $header_lines = explode("\r\n", $headers_raw);
        foreach ($header_lines as $header_line) {
            if (stripos($header_line, 'Transfer-Encoding:') === 0) {
                continue;
            }
            if (stripos($header_line, 'Content-Length:') === 0) {
                continue;
            }
            if (stripos($header_line, 'Connection:') === 0) {
                continue;
            }
            if (!empty($header_line)) {
                header($header_line);
            }
        }
        curl_close($ch);

        /** Output the response body */
        echo $body;
        exit;
    }

    /**
     * @return string
     */
    private static function getTagId():string
    {
        $id = '';
        $pathes = explode('/', $_SERVER['REQUEST_URI']);
        foreach ($pathes as $i => $path) {
            if ($path == trim(self::PATH, '/')) {
                $id = $pathes[$i-1];
                break;
            }
        }

        return 'GTM-' . strrev($id);
    }

    /**
     * @return string
     */
    private static function getIp()
    {
        if (null === self::$ip) {
            self::$ip = '';
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                self::$ip = (string)$_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                self::$ip = (string)$_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
                self::$ip = (string)$_SERVER['HTTP_X_FORWARDED'];
            } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                self::$ip = (string)$_SERVER['HTTP_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
                self::$ip = (string)$_SERVER['HTTP_FORWARDED'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                self::$ip = (string)$_SERVER['REMOTE_ADDR'];
            }
        }
        return self::$ip;
    }

    /**
     * @return string
     */
    private static function getCookieString(): string
    {
        $cookiesString = '';
        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[] = $key . '=' . $value;
        }
        if ($cookies) {
            $cookiesString = implode('; ', $cookies);
        }

        return $cookiesString;
    }

    /**
     * @param string $filename
     * @return string
     */
    private static function findGeoIpFile(string $filename): string
    {
        $filePath = BP . '/var/magefan/geoip/' . $filename;
        if (file_exists($filePath)) {
            return $filePath;
        }

        return '';
    }

    /**
     * @return string
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    private static function getCountryCode(): string
    {
        if (null === self::$countryCode) {
            self::$countryCode = '';
            $path = self::findGeoIpFile('GeoLite2-Country.mmdb');
            if ($path) {
                try {
                    $reader = new \GeoIp2\Database\Reader($path);
                    $country = $reader->country(self::getIp());
                    if ($country && !empty($country->country) && !empty($country->country->isoCode)) {
                        self::$countryCode = $country->country->isoCode;
                        self::$countryCode = (string)self::$countryCode;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return self::$countryCode;
    }

    /**
     * @return string|null
     * @throws \GeoIp2\Exception\AddressNotFoundException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    private static function getRegionCode(): string
    {
        if (null === self::$regionCode) {
            self::$regionCode = '';
            $path = self::findGeoIpFile('GeoLite2-City.mmdb');
            if ($path) {
                try {
                    $reader = new \GeoIp2\Database\Reader($path);
                    $region = $reader->city(self::getIp());
                    if ($region && !empty($region->subdivisions) && isset($region->subdivisions[0]) && !empty($region->subdivisions[0]->isoCode)) {
                        self::$regionCode = $region->subdivisions[0]->isoCode;
                        self::$regionCode = (string)self::$regionCode;
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return self::$regionCode;
    }
}
