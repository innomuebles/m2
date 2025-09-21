<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\DataLayer;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManagerExtra\Api\DataLayer\LoginInterface;

class Login extends AbstractDataLayer implements LoginInterface
{
    /**
     * @inheritDoc
     */
    public function get($customer): array
    {
        return $this->eventWrap([
            'event' => 'login',
            'method' =>  $this->getMethod() ?: 'Login Form',
            'customer_id' => $customer->getId()
        ]);
    }

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        $vendors = [
            'plumrocket/module-pslogin' =>'Plumrocket/SocialLogin',
            'plumrocket/module-psloginpro' =>'Plumrocket/SocialLoginPro',
            'mageplaza/magento-2-social-login' => 'Mageplaza/SocialLogin',
            'amasty/social-login' => 'Amasty/SocialLogin',
            'aheadworks/module-social-login' => 'Aheadworks/SocialLogin',
            'weltpixel/module-social-login' => 'WeltPixel/SocialLogin'
        ];
        $backtrace = \Magento\Framework\Debug::backtrace(true, true, false);
        foreach ($vendors as $vendor => $app) {
            if (strpos($backtrace, $vendor) !== false || strpos($backtrace, $app) !== false) {
                return str_replace('/', ' ', $vendor);
            }
        }
        return '';
    }
}
