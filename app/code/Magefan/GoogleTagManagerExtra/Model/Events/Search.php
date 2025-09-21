<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\SearchInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\DataLayer\SearchTerm as SearchTermDataLayer;

/**
 * Abstract management model
 */
class Search implements SearchInterface
{

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SearchTermDataLayer
     */
    protected $searchTerm;

    /**
     * @param ServerTracker $serverTracker
     * @param Config $config
     * @param SearchTermDataLayer $searchTerm
     */
    public function __construct(
        ServerTracker $serverTracker,
        Config $config,
        SearchTermDataLayer $searchTerm
    ) {
        $this->serverTracker = $serverTracker;
        $this->config = $config;
        $this->searchTerm = $searchTerm;
    }

    /**
     * @param string $searchTerm
     * @return mixed
     */
    public function execute(string $searchTerm)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $data = $this->searchTerm->get($searchTerm);
            $this->serverTracker->push($data);
        }
    }
}
