<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\ServerTracker;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Customer\Model\Session;

class ClientIdProvider
{
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param RemoteAddress $remoteAddress
     * @param CookieManagerInterface $cookieManager
     * @param HttpRequest $httpRequest
     * @param Session $session
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        CookieManagerInterface $cookieManager,
        HttpRequest $httpRequest,
        Session $session
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->cookieManager = $cookieManager;
        $this->httpRequest = $httpRequest;
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function get()
    {
        if (($result = $this->getFromCookies()) || ($result = $this->session->getMfClientId())) {
            $this->set($result);
            return $result;
        }

        //$result = $this->session->getMfGtm4ClientId();
        if (!$result) {
            $result = $this->generate();
            $this->set($result);
        }

        return $result;
    }

    /**
     * @param string $value
     * @return ClientIdProvider
     */
    public function set(string $value): ClientIdProvider
    {
        $this->session->setMfClientId($value);
        return $this;
    }

    /**
     * @return string
     */
    private function getFromCookies(): string
    {
        $raw = $this->cookieManager->getCookie('_ga');
        if (!$raw) {
            $result = '';
        } else {
            $match = null;
            preg_match('/(\d+\.\d+)$/', $raw, $match);
            $result = ($match) ? $match[1] : '';
        }

        return $result;
    }

    /**
     * @return string
     */
    private function generate(): string
    {
        $userAgent = $this->httpRequest->getServer('HTTP_USER_AGENT')
            . http_build_query($_COOKIE)
            . $this->remoteAddress->getRemoteAddress();

        $lengthA = strlen($userAgent);
        $lengthB = (isset($_SESSION) && count($_SESSION) > 0) ? count($_SESSION) : 1;

        for ($c = $lengthB; $c > 0; $c--) {
            $userAgent .= $c ^ $lengthA++;
        }

        $p1 = ($this->hd() ^ $this->la($userAgent) & 2147483647);
        if ($p1 < 1000000000) {
            $p1 *= 10;
        }

        return $p1 . "." . round(microtime(true));
    }

    /**
     * @return float
     */
    private function hd(): float
    {
        return round(2147483647 * random_int(0, mt_getrandmax()) / mt_getrandmax());
    }

    /**
     * @param string $a
     * @return int
     */
    private function la(string $a): int
    {
        $b = 1;
        if ($a) {
            for ($b = 0, $c = strlen($a) - 1; $c >= 0; $c--) {
                $d = ord($a[$c]);
                $b = ($b << 6 & 268435455) + $d + ($d << 14);
                $d = $b & 266338304;
                $b = $d !== 0 ? $b ^ $d >> 21 : $b;
            }
        }
        return $b;
    }
}
