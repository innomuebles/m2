<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magento\Customer\CustomerData;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magento\Customer\Model\Session as Session;
use Magento\Framework\App\RequestInterface;
use Magefan\GoogleTagManagerPlus\Plugin\Magento\CustomerDataPlugin;

class CustomerPlugin extends CustomerDataPlugin
{
    public function __construct(
        RequestInterface $request,
        Session $session,
        SessionManagerInterface $sessionManager,
        Config $config
    ) {
        parent::__construct($request, $session, $sessionManager, $config);
    }
}
