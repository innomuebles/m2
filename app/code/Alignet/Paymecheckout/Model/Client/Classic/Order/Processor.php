<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Alignet\Paymecheckout\Model\Client\Classic\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use \Alignet\Paymecheckout\Model\Client\Classic\Order;

class Processor
{
    /**
     * @var \Alignet\Paymecheckout\Model\Order\Processor
     */
    protected $orderProcessor;

    function __construct(
        \Alignet\Paymecheckout\Model\Order\Processor $orderProcessor
    ) {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @param float $amount
     * @param bool $newest
     * @return bool
     * @throws LocalizedException
     */
    function processStatusChange($paymecheckoutOrderId, $status = '', $amount = null, $newest = true)
    {
        if (!in_array($status, [
            Order::STATUS_NEW,
            Order::STATUS_PENDING,
            Order::STATUS_CANCELLED,
            Order::STATUS_REJECTED,
            Order::STATUS_WAITING,
            Order::STATUS_REJECTED_CANCELLED,
            Order::STATUS_COMPLETED,
            Order::STATUS_ERROR
        ])
        ) {
            throw new LocalizedException(new Phrase('Invalid status.'));
        }
        if (!$newest) {
            $close = in_array($status, [
                Order::STATUS_CANCELLED,
                Order::STATUS_REJECTED,
                Order::STATUS_COMPLETED
            ]);
            $this->orderProcessor->processOld($paymecheckoutOrderId, $status, $close);
            return true;
        }
        switch ($status) {
            case Order::STATUS_NEW:
            case Order::STATUS_PENDING:
                $this->orderProcessor->processPending($paymecheckoutOrderId, $status);
                return true;
            case Order::STATUS_CANCELLED:
            case Order::STATUS_REJECTED:
            case Order::STATUS_REJECTED_CANCELLED:
            case Order::STATUS_ERROR:
                $this->orderProcessor->processHolded($paymecheckoutOrderId, $status);
                return true;
            case Order::STATUS_WAITING:
                $this->orderProcessor->processWaiting($paymecheckoutOrderId, $status);
                return true;
            case Order::STATUS_COMPLETED:
                $this->orderProcessor->processCompleted($paymecheckoutOrderId, $status, $amount);
                return true;
        }
    }
}
