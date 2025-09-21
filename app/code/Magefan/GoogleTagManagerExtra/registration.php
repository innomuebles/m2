<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;
use Magefan\GoogleTagManagerExtra\Model\GoogleTagGateway;

if (GoogleTagGateway::isProxyRequest()) {
    GoogleTagGateway::execute();
}

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Magefan_GoogleTagManagerExtra', __DIR__);
