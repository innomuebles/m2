<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Amasty_SortingGraphQl::Test/GraphQl/_files/am_sort_customer_quote_for_order.php'
);

$objectManager = Bootstrap::getObjectManager();

/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);

/** @var CartManagementInterface $quoteManagement */
$quoteManagement = $objectManager->get(CartManagementInterface::class);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);

$customer = $customerRepository->get('customer_with_addresses@test.com');
$quote = $quoteRepository->getActiveForCustomer($customer->getId());

$quoteManagement->placeOrder($quote->getId());
