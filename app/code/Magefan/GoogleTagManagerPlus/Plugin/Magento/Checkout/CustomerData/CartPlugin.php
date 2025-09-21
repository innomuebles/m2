<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magento\Checkout\CustomerData;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magento\Checkout\Model\Session as Session;
use Magento\Framework\App\RequestInterface;
use Magefan\GoogleTagManagerPlus\Plugin\Magento\CustomerDataPlugin;

class CartPlugin extends CustomerDataPlugin
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
