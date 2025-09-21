<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magefan\GoogleTagManager\Model\DataLayer;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\CustomerFactory;
use Magefan\GoogleTagManagerPlus\Model\GetCustomerData;


class AddCustomerDataPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var GetCustomerData
     */
    private $getCustomerData;


    /**
     * @param RequestInterface $request
     * @param CustomerFactory $customerFactory
     * @param GetCustomerData $getCustomerData
     */
    public function __construct(
        RequestInterface $request,
        CustomerFactory $customerFactory,
        GetCustomerData $getCustomerData
    )
    {
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->getCustomerData = $getCustomerData;
    }

    /**
     * @param $subject
     * @param $result
     * @return array
     */
    public function afterGet($subject, $data, $object): array
    {
        if (!$data) {
            return $data;
        }

        if ($this->isEvent(['Purchase', 'Refund'], $subject)) {
            return $this->addCustomerData(
                $this->getCustomerData->executeOrder($object),
                $data
            );
        } elseif ($this->isEvent(['AddToCart', 'RemoveFromCart'], $subject)) {
            return $this->addCustomerData(
                $this->getCustomerData->executeQuote($object->getQuote()),
                $data
            );
        } elseif ($this->isEvent(['ViewCart', 'BeginCheckout'], $subject)) {
            return $this->addCustomerData(
                $this->getCustomerData->executeQuote($object),
                $data
            );
        } elseif ($this->isEvent(['Login', 'SignUp'], $subject)) {
            return $this->addCustomerData(
                $this->getCustomerData->executeCustomer($object),
                $data
            );
        } elseif ($this->isEvent(['AddToWishlist'], $subject)) {
            $wishlist = $object->getWishlist();
            $customerId = $wishlist->getCustomerId();
            $customer = $this->customerFactory->create()->load($customerId);

            return $this->addCustomerData(
                $this->getCustomerData->executeCustomer($customer),
                $data
            );
        } elseif ($this->isEvent(['ViewItem', 'ViewItemList', 'SearchTerm'], $subject)) {

            $noCache = ($this->request->isXmlHttpRequest()
                || $this->request->getModuleName() == 'checkout'
            );

            if ($noCache) {
                return $this->addCustomerData(
                    $this->getCustomerData->executeSession(),
                    $data
                );
            } else {
                return $this->addCustomerJsData($data);
            }
        }
        return $data;
    }

    /**
     * @param $list
     * @param $object
     * @return bool
     */
    private function isEvent($list, $subject): bool
    {
        foreach ($list as $event) {
            if (false !== strpos(get_class($subject), '\DataLayer\\' . $event)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $customerData
     * @param array $data
     * @return array
     */
    protected function addCustomerData(array $customerData, array $data): array
    {
        foreach ($customerData as $key => $value) {
            if (empty($data[$key])) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addCustomerJsData(array $data): array
    {
        foreach (array_keys(GetCustomerData::CUSTOMER_DATA) as $key) {
            if (!isset($data[$key])) {
                $data[$key] = 'getMfGtmCustomerData().' . $key;
            }
        }
        return $data;
    }
}