<?php

namespace PayU\PaymentGateway\Model;

use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use PayU\PaymentGateway\Api\CreateOrderResolverInterface;
use PayU\PaymentGateway\Api\GetAvailableLocaleInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\Store;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use PayU\PaymentGateway\Api\PayUGetMultiCurrencyPricingInterface;
use PayU\PaymentGateway\Api\PayUMcpExchangeRateResolverInterface;

/**
 * Class CreateOrderResolver
 * @package PayU\PaymentGateway\Model
 */
class CreateOrderResolver implements CreateOrderResolverInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var GetAvailableLocaleInterface
     */
    private $availableLocale;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var OrderAdapterInterface
     */
    private $order;

    /**
     * @var PayUGetMultiCurrencyPricingInterface
     */
    private $currencyPricing;

    /**
     * @var PayUMcpExchangeRateResolverInterface
     */
    private $exchangeRateResolver;

    /**
     * @var PayUConfigInterface
     */
    private $payUConfig;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $http;

    /**
     * CreateOrderResolver constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param GetAvailableLocaleInterface $availableLocale
     * @param OrderRepositoryInterface $orderRepository
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param PayUGetMultiCurrencyPricingInterface $currencyPricing
     * @param PayUMcpExchangeRateResolverInterface $exchangeRateResolver
     * @param PayUConfigInterface $payUConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Request\Http $http
     */
    public function __construct(
        UrlInterface $urlBuilder,
        GetAvailableLocaleInterface $availableLocale,
        OrderRepositoryInterface $orderRepository,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        PayUGetMultiCurrencyPricingInterface $currencyPricing,
        PayUMcpExchangeRateResolverInterface $exchangeRateResolver,
        PayUConfigInterface $payUConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $http
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->availableLocale = $availableLocale;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->currencyPricing = $currencyPricing;
        $this->exchangeRateResolver = $exchangeRateResolver;
        $this->payUConfig = $payUConfig;
        $this->store = $storeManager->getStore();
        $this->http  = $http;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(
        OrderAdapterInterface $order,
        $methodTypeCode,
        $methodCode,
        $totalDue = null,
        $orderCurrencyCode = null,
        $coninueUrl = 'checkout/onepage/success'
    ) {
        $this->order = $order;

        $paymentData = [
            'txn_type' => 'A',
            'description' => $this->getOrderDescription(),
            'customerIp' => $this->getIp(),
            'extOrderId' => $this->getExtOrderId(),
            'totalAmount' => $this->getFormatAmount($this->order->getGrandTotalAmount()),
            'currencyCode' => $this->order->getCurrencyCode(),
            'notifyUrl' => $this->getNotifyUrl($methodTypeCode, $methodCode),
            'continueUrl' => $this->urlBuilder->getUrl($coninueUrl),
            'settings' => ['invoiceDisabled' => 'true'],
            'buyer' => $this->getBuyer(),
            'products' => $this->getProductArray(),
        ];
        if ($this->isPayUMethod($methodTypeCode, $methodCode)) {
            $paymentData['payMethods']['payMethod'] = ['type' => $methodTypeCode, 'value' => $methodCode];
        }
        if ($orderCurrencyCode === null) {
            $orderCurrencyCode = $this->store->getCurrentCurrencyCode();
        }
        if ($methodTypeCode === PayUConfigInterface::PAYU_CC_TRANSFER_KEY &&
            $this->order->getCurrencyCode() !== $orderCurrencyCode) {
            $paymentData['mcpData'] = $this->getMcpData($totalDue);
        }

        return $paymentData;
    }

    /**
     * Get description for order
     *
     * @return Phrase
     */
    private function getOrderDescription()
    {
        $shopUrl = str_replace('www.', '', parse_url($this->urlBuilder->getBaseUrl(), PHP_URL_HOST));

        return __('Order %1 [%2]', $this->order->getOrderIncrementId(), $shopUrl);
    }

    /**
     * @return array
     */
    private function getBuyer()
    {
        $address = $this->order->getShippingAddress();
        $billingAddress = $this->order->getBillingAddress();

        $result[static::BUYER_EMAIL] = $address === null
            ? $this->customerSession->getCustomer()->getEmail()
            : $address->getEmail();

        if ($billingAddress !== null) {
            $result['firstName'] = $billingAddress->getFirstname();
            $result['lastName'] = $billingAddress->getLastname();
        } elseif ($address !== null) {
            $result['firstName'] = $address->getFirstname();
            $result['lastName'] = $address->getLastname();
        }

        $result['extCustomerId'] = $this->order->getCustomerId();
        $result['language'] = $this->availableLocale->execute();

        return $result;
    }

    /**
     * Get external order ID with increment notation
     *
     * @return string
     */
    private function getExtOrderId()
    {
        return $this->order->getOrderIncrementId() . '_' . time();
    }

    /**
     * Get amount in proper format
     *
     * @param $amount
     *
     * @return string
     */
    private function getFormatAmount($amount)
    {
        return number_format(($amount * 100), 0, '.', '');
    }

    /**
     * Get notify url
     *
     * @param string $methodTypeCode
     * @param string $methodCode
     *
     * @return string
     */
    private function getNotifyUrl($methodTypeCode, $methodCode)
    {
        if ($this->isPayUMethod($methodTypeCode, $methodCode)) {
            $parameters['type'] = $methodTypeCode;
        }
        $parameters['store'] = $this->order->getStoreId();

        return $this->urlBuilder->getUrl('payu/data/getNotify', $parameters);
    }

    /**
     * Is order is type of PayU methods
     *
     * @param string $methodTypeCode
     * @param string $methodCode
     *
     * @return bool
     */
    private function isPayUMethod($methodTypeCode, $methodCode)
    {
        return !empty($methodCode) && !empty($methodTypeCode);
    }

    /**
     * Get product array with name, price and product quantity
     *
     * @return array
     */
    private function getProductArray()
    {
        return [
            [
                'name' => $this->getOrderDescription(),
                'unitPrice' => $this->getFormatAmount($this->order->getGrandTotalAmount()),
                'quantity' => 1
            ]
        ];
    }

    /**
     * Get MCP DATA
     *
     * @param null|float $totalDue
     *
     * @return array
     */
    private function getMcpData($totalDue = null)
    {
        $rates = $this->currencyPricing->execute();
        $mcpRate = $this->exchangeRateResolver->resolve(
            $this->store->getCurrentCurrencyCode(),
            $this->order->getCurrencyCode()
        );

        return [
            'mcpCurrency' => $this->store->getCurrentCurrencyCode(),
            'mcpAmount' => $this->getFormatAmount(
                $totalDue !== null ? $totalDue :
                    $this->checkoutSession->getQuote()->getShippingAddress()->getGrandTotal()
            ),
            'mcpRate' => $mcpRate,
            'mcpFxTableId' => $rates->id,
            'mcpPartnerId' => $this->payUConfig->getMultiCurrencyPricingPartnerId()
        ];
    }

    /**
     * @return string
     */
    private function getIp()
    {
        $ip = explode(',', trim($this->http->getClientIp()));

        return filter_var($ip[0], FILTER_VALIDATE_IP) ? $ip[0] : '127.0.0.1';
    }
}
