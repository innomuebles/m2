<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Block\Widget;

use Amasty\Sorting\Model\Source\SortOrder;
use Amasty\Sorting\ViewModel\Helpers;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Product\Compare;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\DB\Select;

class Featured extends ProductsList
{
    public const DEFAULT_COLLECTION_SORT_BY = 'name';
    public const DEFAULT_COLLECTION_ORDER = SortOrder::SORT_ASC;
    public const IMAGE_TYPE = 'featured_products_sidebar';

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        $collection = parent::createCollection();
        $collection->getSelect()->reset(Select::ORDER);
        $collection->setOrder($this->getSortBy(), $this->getSortOrder());
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        if (!$this->hasData('sort_by')) {
            $this->setData('sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('sort_by');
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        if (!$this->hasData('amsorting_sort_order')) {
            $this->setData('amsorting_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('amsorting_sort_order');
    }

    /**
     * @param ProductInterface $product
     * @return Image
     */
    public function getImageModel(ProductInterface $product): Image
    {
        return $this->_imageHelper->init($product, self::IMAGE_TYPE);
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getProductName(ProductInterface $product): string
    {
        /** @var Helpers|null $helper */
        $helper = $this->getHelpers();

        return $helper
            ? $helper->getProductAttribute($product, $product->getName(), ProductInterface::NAME)
            : '';
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getPostData(ProductInterface $product): string
    {
        /** @var Helpers|null $helper */
        $helper = $this->getHelpers();

        return $helper
            ? $helper->getPostData($this->getAddToCartUrl($product), (int) $product->getEntityId())
            : '';
    }

    public function isWishListAllow(): bool
    {
        /** @var Helpers|null $helper */
        $helper = $this->getHelpers();

        return $helper ? $helper->getWishlistHelper()->isAllow() : false;
    }

    private function getHelpers(): ?Helpers
    {
        return $this->getData('helpers');
    }

    public function getCompareHelper(): ?Compare
    {
        /** @var Helpers|null $helper */
        $helper = $this->getHelpers();
        return $helper ? $helper->getCompareHelper() : null;
    }
}
