<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Alignet\Paymecheckout\Model\Order;

class Validator
{
    /**
     * @var \Alignet\Paymecheckout\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    function __construct(
        \Alignet\Paymecheckout\Model\ResourceModel\Transaction $transactionResource,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->transactionResource = $transactionResource;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    function validateNoTransactions(\Magento\Sales\Model\Order $order)
    {
        return $this->transactionResource->getLastPayuplOrderIdByOrderId($order->getId()) === false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    function validatePaymentMethod(\Magento\Sales\Model\Order $order)
    {
        return $order->getPayment()->getMethod() === \Alignet\Paymecheckout\Model\Paymecheckout::CODE;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    function validateState(\Magento\Sales\Model\Order $order)
    {
        return !in_array($order->getState(), [
            \Magento\Sales\Model\Order::STATE_CANCELED,
            \Magento\Sales\Model\Order::STATE_CLOSED,
            \Magento\Sales\Model\Order::STATE_COMPLETE
        ]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    function validateCustomer(\Magento\Sales\Model\Order $order)
    {
        return $order->getCustomerId() === $this->customerSession->getCustomerId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    function validateNotPaid(\Magento\Sales\Model\Order $order)
    {
        return !$order->getTotalPaid();
    }
}
