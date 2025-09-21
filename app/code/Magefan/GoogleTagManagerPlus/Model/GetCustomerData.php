<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\GroupRegistry as GroupRegistry;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Customer\Model\CustomerFactory;

class GetCustomerData
{
    const CUSTOMER_DATA = [
        'customer_firstname' => "getFirstname",
        'customer_lastname' => "getLastname",
        'customer_email' => "getEmail",
        'customer_dob' => "getDob",
        'customer_gender' => "getGender",
        'customer_telephone' => "getTelephone",
        'customer_postcode' => "getPostcode",
        'customer_city' => "getCity",
        'customer_region' => "getRegionCode",
        'customer_country_id' => "getCountryId",
        'customer_identifier' => "getEmail",
        'customerGroup' => "getGroupId",
    ];

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var GroupRegistry
     */
    private $customerGroupRegistry;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @param Session $session
     * @param CheckoutSession $checkoutSession
     * @param AddressRepositoryInterface $addressRepository
     * @param GroupRegistry $customerGroupRegistry
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Session                     $session,
        CheckoutSession             $checkoutSession,
        AddressRepositoryInterface  $addressRepository,
        GroupRegistry               $customerGroupRegistry,
        CustomerFactory             $customerFactory
    )
    {
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
        $this->addressRepository = $addressRepository;
        $this->customerGroupRegistry = $customerGroupRegistry;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @return array|void
     */
    public function executeSession()
    {
        if ($this->session->isLoggedIn()) {
            $data = $this->executeCustomer($this->session->getCustomer());
        } else {
            $data = $this->executeQuote($this->checkoutSession->getQuote());
        }

        return $data;
    }

    /**
     * @param $customer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeCustomer($customer): array
    {
        if (!($customer instanceof \Magento\Customer\Model\Customer)) {
            /* Convert object to Magento\Customer\Model\Customer */
            $customer = $this->customerFactory->create()->load($customer->getId());
        }

        $address = $this->chooseAddress($customer->getDefaultBillingAddress(), $customer->getDefaultShippingAddress());
        return $this->getAdvancedData($customer, $address);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    public function executeOrder(\Magento\Sales\Api\Data\OrderInterface $order): array
    {
        $address = $this->chooseAddress($order->getBillingAddress(), $order->getShippingAddress());
        return $this->getAdvancedData($order, $address);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     */
    public function executeQuote(\Magento\Quote\Api\Data\CartInterface $quote): array
    {
        $address = $this->chooseAddress($quote->getBillingAddress(), $quote->getShippingAddress());
        return $this->getAdvancedData($quote, $address);
    }

    /**
     * @param $billingAddress
     * @param $shippingAddress
     * @return DataObject|mixed
     */
    private function chooseAddress($billingAddress, $shippingAddress)
    {
        if ($billingAddress && $billingAddress->getFirstname()) {
            return $billingAddress;
        } elseif ($shippingAddress && $shippingAddress->getFirstname()) {
            return $shippingAddress;
        } else {
            return new DataObject();
        }
    }

    /**
     * @param $entity
     * @param $address
     * @return array
     */
    private function getAdvancedData($entity, $address): array
    {
        $data = [];
        foreach (self::CUSTOMER_DATA as $parameter => $method) {
            if ($entity instanceof \Magento\Customer\Model\Data\Customer && 'getRegionCode' === $method) {
                $method = 'getRegion';
            }

            $customerMethod = str_replace('get', 'getCustomer', $method);
            $value = $entity->$customerMethod();

            if (!$value) {
                $value = $address->$method();
            }
            if (!$value) {
                $value = $entity->$method();
            }

            if ( (!$value || $value == 'not logged in') && 'customerGroup' === $parameter) {
                $value = 'Guest';
            }

            if (!$value) {
                continue;
            }

            $value = $this->prepareCustomerData($method, $value);
            if ('customerGroup' !== $parameter) {
                $value =  hash('sha256', (string)$value);
            }

            $data[$parameter] = $value;
        }
        return $data;
    }

    /**
     * @param $method
     * @param $value
     * @return string
     */
    private function prepareCustomerData($method, $value): string
    {
        if ('getRegion' == $method && is_object($value)) {
            $value = $value->getRegionCode() ?: '';
        } elseif ('getGender' == $method) {
            $value = ($value == 1) ? 'f' : 'm';
        } elseif (in_array($method, ['getTelephone', 'getDob'])) {
            $value = str_replace(['-', '(', ')', ' '], "", (string)$value);
        } elseif ('getGroupId' == $method) {
            try {
                $group = $this->customerGroupRegistry->retrieve($value);
                $value = $group->getCode();
                if ('NOT LOGGED IN' == $value) {
                    $value = 'Guest';
                }
            } catch (\Exception $e) {
                $value = 'Guest';
            }
        }

        $preparesString = strtolower(trim((string)$value));
        return mb_convert_encoding($preparesString, "UTF-8");
    }
}
