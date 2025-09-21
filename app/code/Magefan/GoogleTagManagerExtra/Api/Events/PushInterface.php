<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Api\Events;

/**
 * Interface PushInterface
 */
interface PushInterface
{
    /**
     * @return mixed
     */
    public function execute();
}
