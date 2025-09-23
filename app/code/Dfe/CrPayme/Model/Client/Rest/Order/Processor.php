<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\Rest\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use \Dfe\CrPayme\Model\Client\Rest\Order;

class Processor
{
    /**
     * @var \Dfe\CrPayme\Model\Order\Processor
     */
    protected $orderProcessor;

    function __construct(
        \Dfe\CrPayme\Model\Order\Processor $orderProcessor
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
            Order::STATUS_COMPLETED
        ])) {
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
