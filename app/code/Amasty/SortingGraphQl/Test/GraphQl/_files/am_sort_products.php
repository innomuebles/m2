<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var ProductInterfaceFactory $productFactory */
$productFactory = $objectManager->get(ProductInterfaceFactory::class);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

/** @var CategoryInterfaceFactory $categoryFactory */
$categoryFactory = $objectManager->get(CategoryInterfaceFactory::class);

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);

// create first category
$category = $categoryFactory->create();
$category->isObjectNew(true);
$category->setName('Am Category Sorting One Test')
    ->setIsActive(true)
    ->setPosition(1);
$category = $categoryRepository->save($category);
$categoryId = $category->getId();

// create second category
$categorySecond = $categoryFactory->create();
$categorySecond->isObjectNew(true);
$categorySecond->setName('Am Category Sorting Second Test')
    ->setIsActive(true)
    ->setPosition(1);
$categorySecond = $categoryRepository->save($categorySecond);
$categorySecondId = $categorySecond->getId();

// create products data array
$productsData = [
    [
        'sku' => 'am_sort_simple_1',
        'category' => [$categoryId],
        'type' => Type::TYPE_SIMPLE,
        'name' => 'Amasty Simple Product 1',
        'price' => 1000
    ],
    [
        'sku' => 'am_sort_simple_2',
        'category' => [$categorySecondId],
        'type' => Type::TYPE_SIMPLE,
        'name' => 'Amasty Simple Product 2',
        'price' => 1200
    ],
    [
        'sku' => 'am_sort_simple_3',
        'category' => [$categoryId],
        'type' => Type::TYPE_SIMPLE,
        'name' => 'Amasty Simple Product 3',
        'price' => 1200
    ],
    [
        'sku' => 'am_sort_simple_4',
        'category' => [$categorySecondId],
        'type' => Type::TYPE_SIMPLE,
        'name' => 'Amasty Simple Product 4',
        'price' => 2000
    ],
    [
        'sku' => 'am_sort_simple_5',
        'category' => [$categoryId],
        'type' => Type::TYPE_SIMPLE,
        'name' => 'Amasty Simple Product 5',
        'price' => 5000
    ],
    [
        'sku' => 'am_sort_virtual_5',
        'category' => [$categorySecondId],
        'type' => Type::TYPE_VIRTUAL,
        'name' => 'Amasty Virtual Product 1',
        'price' => 1000
    ],
    [
        'sku' => 'am_sort_virtual_2',
        'category' => [$categoryId],
        'type' => Type::TYPE_VIRTUAL,
        'name' => 'Amasty Virtual Product 2',
        'price' => 2500
    ],
    [
        'sku' => 'am_sort_virtual_3',
        'category' => [$categorySecondId],
        'type' => Type::TYPE_VIRTUAL,
        'name' => 'Amasty Virtual Product 3',
        'price' => 2500
    ],
    [
        'sku' => 'am_sort_virtual_4',
        'category' => [$categoryId],
        'type' => Type::TYPE_VIRTUAL,
        'name' => 'Amasty Virtual Product 4',
        'price' => 50000
    ],
    [
        'sku' => 'am_sort_virtual_1',
        'category' => [$categorySecondId],
        'type' => Type::TYPE_VIRTUAL,
        'name' => 'Amasty Virtual Product 5',
        'price' => 800
    ]
];

// create products
foreach ($productsData as $productData) {
    $product = $productFactory->create();
    $product->setTypeId($productData['type'])
        ->setAttributeSetId($product->getDefaultAttributeSetId())
        ->setWebsiteIds([1])
        ->setName($productData['name'])
        ->setSku($productData['sku'])
        ->setPrice(10)
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setPrice($productData['price'])
        ->setStockData(
            [
                'use_config_manage_stock'   => 1,
                'qty'                       => 100,
                'is_qty_decimal'            => 0,
                'is_in_stock'               => 1,
            ]
        )
        ->setCategoryIds($productData['category']);
    $productRepository->save($product);
}
