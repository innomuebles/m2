<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Cron;

use Magento\Framework\App\ResourceConnection;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;

class DeleteOldSessions
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ServerTracker
     */
    protected $serverTracker;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Config $config
     * @param ServerTracker $serverTracker
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $config,
        ServerTracker $serverTracker
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
        $this->serverTracker = $serverTracker;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $date = new \DateTime();
            $date->modify('-1 year');
            $formattedDate = $date->format('Y-m-d H:i:s');

            $condition = ['session_id < ?' => $formattedDate];
            $tableName = $this->resourceConnection->getTableName('magefan_gtm_session');

            $this->resourceConnection->getConnection()
                ->delete($tableName, $condition);
        }
    }
}
