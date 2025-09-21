<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magefan\GoogleTagManagerPlus\Api;

use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magefan\GoogleTagManagerPlus\Api\SessionManagerInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;

/**
 * Class SessionManagerPlugin
 */
class SessionManagerInterfacePlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigExtra
     */
    private $configExtra;

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @param Config $config
     * @param ConfigExtra $configExtra
     * @param ServerTracker $serverTracker
     */
    public function __construct(
        Config $config,
        ConfigExtra $configExtra,
        ServerTracker $serverTracker
    ) {
        $this->config = $config;
        $this->configExtra = $configExtra;
        $this->serverTracker = $serverTracker;
    }

    /**
     * Generate JSON container
     *
     * @param SessionManager $subject
     * @return void
     */
    public function afterPush(SessionManagerInterface $subject, $result, $session, $data): void
    {
        if ($this->config->isEnabled()
            && $this->configExtra->isHeadlessStorefront()
            && $this->serverTracker->isEnabled()
        ) {
            $this->serverTracker->push($data);
        }
    }
}
