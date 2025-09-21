<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_with_addresses.php');
Resolver::getInstance()->requireDataFixture('Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_products.php');

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);

/** @var AddressInterface $quoteShippingAddress */
$quoteShippingAddress = $objectManager->get(AddressInterfaceFactory::class)->create();

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);

/** @var AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->get(AddressRepositoryInterface::class);

/** @var SearchCriteriaBuilder $searchCriteria */
$searchCriteria = $objectManager->get(SearchCriteriaBuilder::class);

$productRepository->cleanCache();
$searchCriteriaAddress = $searchCriteria->addFilter('postcode', '75477')
    ->addFilter('city', 'CityM')
    ->addFilter('country_id', 'US')
    ->addFilter('firstname', 'John')
    ->addFilter('street', 'CustomerAddress1')
    ->create();
$address = $addressRepository->getList($searchCriteriaAddress)->getItems()[0];
$quoteShippingAddress->importCustomerAddressData($address);
$customer = $customerRepository->get('customer_with_addresses@test.com');

/** @var CartInterface $quote */
$quote = $objectManager->get(CartInterfaceFactory::class)->create();

$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(0)
    ->assignCustomerWithAddressChange($customer)
    ->setShippingAddress($quoteShippingAddress)
    ->setBillingAddress($quoteShippingAddress)
    ->setCheckoutMethod(Onepage::METHOD_CUSTOMER)
    ->setReservedOrderId('123456789')
    ->setEmail($customer->getEmail());
$quote->addProduct($productRepository->get('am_sort_simple_4'), 3);
$quote->addProduct($productRepository->get('am_sort_virtual_2'), 1);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getShippingAddress()->collectShippingRates();
$quote->getPayment()->setMethod('checkmo');
$quoteRepository->save($quote);
