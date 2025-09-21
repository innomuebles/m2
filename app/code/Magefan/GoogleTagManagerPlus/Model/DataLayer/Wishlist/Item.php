<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer\Wishlist;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\Wishlist\ItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Item extends AbstractDataLayer implements ItemInterface
{
    /**
     * @inheritDoc
     */
    public function get(\Magento\Wishlist\Model\Item $wishlistItem): array
    {
        $product = $this->getItemProduct($wishlistItem);

        $categoryNames = $this->getCategoryNames($product);
        return array_merge([
            'item_id' => ($this->config->getProductAttribute() == 'sku') ?
                $product->getSku() :
                $product->getData($this->config->getProductAttribute()),
            'item_name' => $product->getName(),
            'item_brand' => $this->config->getBrandAttribute() ?
                $product->getData($this->config->getBrandAttribute()) : '',
            'price' => $this->getProductValue($product),
            'quantity' => $wishlistItem->getQty() * 1
        ], $categoryNames);
    }

    /**
     * @param $wishlistItem
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function getItemProduct($wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        if ('configurable' === $product->getTypeId()) {
            $options = $wishlistItem->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() === 'simple_product') {
                        try {
                            $product = $this->productRepository->getById($option->getProductId());
                            break;
                        } catch (NoSuchEntityException $e) {

                        }
                    }
                }
            }
        }
        return $product;
    }
}
