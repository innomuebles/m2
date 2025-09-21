<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Api;

/**
 * Interface AddBillingInfoInterface
 */
interface ServerTrackerInterface
{
    const REQUESTER_TYPE = 'ServerTracker';

    /**
     * @param array $data
     * @return bool
     */
    public function push(array $data): bool;

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
