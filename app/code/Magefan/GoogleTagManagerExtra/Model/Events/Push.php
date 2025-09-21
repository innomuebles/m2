<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\PushInterface;
use Magefan\GoogleTagManagerExtra\Model\ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\App\RequestInterface;

/**
 * Abstract management model
 */
class Push implements PushInterface
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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ServerTracker $serverTracker
     * @param Config $config
     * @param RequestInterface $request
     */
    public function __construct(
        ServerTracker $serverTracker,
        Config $config,
        RequestInterface $request
    ) {
        $this->serverTracker = $serverTracker;
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $data = json_decode($this->request->getContent(), true);
            $this->serverTracker->push((array)$data);
        }
    }
}
