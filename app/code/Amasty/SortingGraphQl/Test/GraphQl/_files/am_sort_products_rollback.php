<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Catalog\Model\GetCategoryByName;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);

/** @var GetCategoryByName $getCategoryByName */
$getCategoryByName = $objectManager->create(GetCategoryByName::class);

$currentArea = $registry->registry('isSecureArea');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

// get categories
$category = $getCategoryByName->execute('Am Category Sorting One Test');
$categorySecond = $getCategoryByName->execute('Am Category Sorting Second Test');

// delete categories
try {
    $categoryRepository->delete($category);
    $categoryRepository->delete($categorySecond);
} catch (NoSuchEntityException $e) {
    // category already deleted.
}

// products sku data array
$skus = [
    'am_sort_simple_1',
    'am_sort_simple_2',
    'am_sort_simple_3',
    'am_sort_simple_4',
    'am_sort_simple_5',
    'am_sort_virtual_1',
    'am_sort_virtual_2',
    'am_sort_virtual_3',
    'am_sort_virtual_4',
    'am_sort_virtual_5'
];

// delete products
try {
    foreach ($skus as $sku) {
        $productRepository->delete($productRepository->get($sku));
    }
} catch (NoSuchEntityException $e) {
    // product already deleted.
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', $currentArea);
