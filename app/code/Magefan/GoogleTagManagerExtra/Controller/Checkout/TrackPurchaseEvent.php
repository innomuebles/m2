<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;
use Magefan\GoogleTagManager\Api\Transaction\LogInterface as TransactionLog;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;

class TrackPurchaseEvent extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TransactionLog
     */
    private $transactionLog;

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @param Context $context
     * @param Config $config
     * @param TransactionLog $transactionLog
     * @param ServerTracker $serverTracker
     */
    public function __construct(
        Context $context,
        Config $config,
        TransactionLog $transactionLog,
        ServerTracker $serverTracker
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->transactionLog = $transactionLog;
        $this->serverTracker = $serverTracker;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $orderId = (string)$this->getRequest()->getPostValue('transaction_id');
            try {
                $this->transactionLog->logTransaction($orderId, ConfigExtra::PURCHASE_EVENT_TRACKED);
            } catch (NoSuchEntityException $e) {
            }
        }
    }
}
