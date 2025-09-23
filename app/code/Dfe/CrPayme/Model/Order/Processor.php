<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Processor
{
    /**
     * @var \Dfe\CrPayme\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Dfe\CrPayme\Model\Transaction\Service
     */
    protected $transactionService;

    /**
     * @param \Dfe\CrPayme\Model\Order $orderHelper
     * @param \Dfe\CrPayme\Model\Transaction\Service $transactionService
     */
    function __construct(
        \Dfe\CrPayme\Model\Order $orderHelper,
        \Dfe\CrPayme\Model\Transaction\Service $transactionService
    ) {
        $this->orderHelper = $orderHelper;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string$status
     * @param bool $close
     * @throws LocalizedException
     */
    function processOld($paymecheckoutOrderId, $status, $close = false)
    {
        $this->transactionService->updateStatus($paymecheckoutOrderId, $status, $close);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @throws LocalizedException
     */
    function processPending($paymecheckoutOrderId, $status)
    {
        $this->transactionService->updateStatus($paymecheckoutOrderId, $status);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @throws LocalizedException
     */
    function processHolded($paymecheckoutOrderId, $status)
    {
        $order = $this->loadOrderByPayuplOrderId($paymecheckoutOrderId);
        $this->orderHelper->setHoldedOrderStatus($order, $status);
        $this->transactionService->updateStatus($paymecheckoutOrderId, $status, true);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @throws LocalizedException
     * @todo Implement some additional logic for transaction confirmation by store owner.
     */
    function processWaiting($paymecheckoutOrderId, $status)
    {
        $this->transactionService->updateStatus($paymecheckoutOrderId, $status);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @param string $status
     * @param float $amount
     * @throws LocalizedException
     */
    function processCompleted($paymecheckoutOrderId, $status, $amount)
    {
        $order = $this->loadOrderByPayuplOrderId($paymecheckoutOrderId);
        $this->orderHelper->completePayment($order, $amount, $paymecheckoutOrderId);
        $this->transactionService->updateStatus($paymecheckoutOrderId, $status, true);
    }

    /**
     * @param string $paymecheckoutOrderId
     * @return \Dfe\CrPayme\Model\Sales\Order
     * @throws LocalizedException
     */
    protected function loadOrderByPayuplOrderId($paymecheckoutOrderId)
    {
        $order = $this->orderHelper->loadOrderByPayuplOrderId($paymecheckoutOrderId);
        if (!$order) {
            throw new LocalizedException(new Phrase('Order not found.'));
        }
        return $order;
    }
}
