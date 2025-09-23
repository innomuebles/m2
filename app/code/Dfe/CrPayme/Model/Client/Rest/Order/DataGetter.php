<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\Rest\Order;

use Dfe\CrPayme\Model\Client\Rest\Config;

class DataGetter
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var \Dfe\CrPayme\Model\Order\ExtOrderId
     */
    protected $extOrderIdHelper;

    /**
     * @param \Magento\Framework\View\Context $context
     * @param Config $configHelper
     */
    function __construct(
        \Magento\Framework\View\Context $context,
        Config $configHelper,
        \Dfe\CrPayme\Model\Order\ExtOrderId $extOrderIdHelper
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->configHelper = $configHelper;
        $this->extOrderIdHelper = $extOrderIdHelper;
    }

    /**
     * @return string
     */
    function getContinueUrl()
    {
        return $this->urlBuilder->getUrl('orba_payupl/payment/end');
    }

    /**
     * @return string
     */
    function getNotifyUrl()
    {
        return $this->urlBuilder->getUrl('orba_payupl/payment/notify');
    }

    /**
     * @return string
     */
    function getCustomerIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    function getMerchantPosId()
    {
        return $this->configHelper->getConfig('merchant_pos_id');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    function getBasicData(\Magento\Sales\Model\Order $order)
    {
        $incrementId = $order->getIncrementId();
        return [
            'currencyCode' => $order->getOrderCurrencyCode(),
            'totalAmount' => $order->getGrandTotal() * 100,
            'extOrderId' => $this->extOrderIdHelper->generate($order),
            'description' => __('Order # %1', [$incrementId]),
        ];
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    function getProductsData(\Magento\Sales\Model\Order $order)
    {
        /**
         * @var $orderItem \Magento\Sales\Api\Data\OrderItemInterface
         */
        $products = [];
        $orderItems = $order->getAllVisibleItems();
        foreach ($orderItems as $orderItem) {
            $products[] = [
                'name' => $orderItem->getName(),
                'unitPrice' => $orderItem->getPriceInclTax() * 100,
                'quantity' => (float) $orderItem->getQtyOrdered()
            ];
        }
        return $products;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array|null
     */
    function getShippingData(\Magento\Sales\Model\Order $order)
    {
        if ($order->getShippingMethod()) {
            $shippingInclTax = (float) $order->getShippingInclTax();
            if ($shippingInclTax) {
                return [
                    'name' => __('Shipping Method') . ': ' . $order->getShippingDescription(),
                    'unitPrice' => $shippingInclTax * 100,
                    'quantity' => 1
                ];
            }
        }
        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array|null
     */
    function getBuyerData(\Magento\Sales\Model\Order $order)
    {
        /**
         * @var $billingAddress \Magento\Sales\Api\Data\OrderAddressInterface
         */
        $billingAddress = $order->getBillingAddress();
        if ($billingAddress) {
            $buyer = [
                'email' => $billingAddress->getEmail(),
                'phone' => $billingAddress->getTelephone(),
                'firstName' => $billingAddress->getFirstname(),
                'lastName' => $billingAddress->getLastname()
            ];
            return $buyer;
        }
        return null;
    }
}
