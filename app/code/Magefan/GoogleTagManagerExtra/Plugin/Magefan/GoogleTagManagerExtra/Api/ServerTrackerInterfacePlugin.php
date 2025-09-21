<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Plugin\Magefan\GoogleTagManagerExtra\Api;

use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface;
use Magefan\GoogleTagManager\Api\Transaction\LogInterface as TransactionLog;
use Magefan\GoogleTagManagerExtra\Model\Config as ConfigExtra;

;

class ServerTrackerInterfacePlugin
{
    /**
     * @var TransactionLog
     */
    private $transactionLog;

    /**
     * @param TransactionLog $transactionLog
     */
    public function __construct(
        TransactionLog $transactionLog
    ) {
        $this->transactionLog = $transactionLog;
    }

    /**
     * @param ServerTrackerInterface $subject
     * @param $result
     * @param $data
     * @return bool
     */
    public function afterPush(ServerTrackerInterface $subject, $result, $data): bool
    {
        if ($result) {
            if (isset($data['ecommerce']['transaction_id'])) {
                $transactionId = (string)$data['ecommerce']['transaction_id'];
                $this->transactionLog->logTransaction($transactionId, ConfigExtra::PURCHASE_EVENT_TRACKED);
            } else {
                foreach ($data as $_data) {
                    if (isset($_data['ecommerce']['transaction_id'])) {
                        $transactionId = (string)$_data['ecommerce']['transaction_id'];
                        $this->transactionLog->logTransaction($transactionId, ConfigExtra::PURCHASE_EVENT_TRACKED);
                    }
                }
            }
        }

        return $result;
    }
}
