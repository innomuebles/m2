<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Output;
use Magento\Framework\Data\Helper\PostHelper;

class Helpers
{
    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @var PostHelper
     */
    private $postHelper;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    private $wishlistHelper;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    public function __construct(
        Output $outputHelper,
        PostHelper $postHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareHelper
    ) {
        $this->outputHelper = $outputHelper;
        $this->postHelper = $postHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->compareHelper = $compareHelper;
    }

    /**
     * @param ProductInterface $product
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductAttribute(
        ProductInterface $product,
        string $attributeHtml,
        string $attributeName
    ): string {
        return $this->outputHelper->productAttribute($product, $attributeHtml, $attributeName);
    }

    /**
     * @param string $addToCartUrl
     * @param int $entityId
     * @return string
     */
    public function getPostData(string $addToCartUrl, int $entityId): string
    {
        return $this->postHelper->getPostData($addToCartUrl, ['product' => $entityId]);
    }

    /**
     * @return \Magento\Wishlist\Helper\Data
     */
    public function getWishlistHelper(): \Magento\Wishlist\Helper\Data
    {
        return $this->wishlistHelper;
    }

    /**
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    public function getCompareHelper(): \Magento\Catalog\Helper\Product\Compare
    {
        return $this->compareHelper;
    }
}
