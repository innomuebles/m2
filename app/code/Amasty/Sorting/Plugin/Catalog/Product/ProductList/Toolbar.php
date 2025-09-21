<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Catalog\Product\ProductList;

use Amasty\Sorting\Helper\Data;
use Amasty\Sorting\Model\Catalog\Toolbar\GetDefaultDirection;
use Amasty\Sorting\Model\Method\ApplyGlobalSorting;
use Amasty\Sorting\Model\Method\IsAvailableForSorting;
use Amasty\Sorting\Model\ResourceModel\Method\Image as ImageMethod;
use Amasty\Sorting\Model\ResourceModel\Method\Instock as InstockMethod;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Registry;

class Toolbar
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ToolbarModel
     */
    private $toolbarModel;

    /**
     * @var ImageMethod
     */
    private $imageMethod;

    /**
     * @var InstockMethod
     */
    private $stockMethod;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var GetDefaultDirection
     */
    private $getDefaultDirection;

    /**
     * @var ApplyGlobalSorting
     */
    private $applyGlobalSorting;

    /**
     * @var IsAvailableForSorting
     */
    private $isAvailableForSorting;

    public function __construct(
        Data $helper,
        ToolbarModel $toolbarModel,
        ImageMethod $imageMethod,
        InstockMethod $stockMethod,
        Registry $registry,
        GetDefaultDirection $getDefaultDirection,
        ApplyGlobalSorting $applyGlobalSorting,
        IsAvailableForSorting $isAvailableForSorting
    ) {
        $this->helper = $helper;
        $this->toolbarModel = $toolbarModel;
        $this->imageMethod = $imageMethod;
        $this->stockMethod = $stockMethod;
        $this->registry = $registry;
        $this->getDefaultDirection = $getDefaultDirection;
        $this->applyGlobalSorting = $applyGlobalSorting;
        $this->isAvailableForSorting = $isAvailableForSorting;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param string                                             $dir
     *
     * @return string
     */
    public function afterGetCurrentDirection($subject, $dir)
    {
        $defaultDir = $this->getDefaultDirection->execute($subject->getCurrentOrder());
        $subject->setDefaultDirection($defaultDir);

        if (!$this->toolbarModel->getDirection()
            || $this->shouldSetDirection($subject->getCurrentOrder())
        ) {
            $dir = $defaultDir;
        }

        return $dir;
    }

    /**
     * @param string $order
     *
     * @return bool
     */
    private function shouldSetDirection($order)
    {
        return in_array($order, GetDefaultDirection::ALWAYS_DESC)
            || in_array($order, GetDefaultDirection::ALWAYS_ASC);
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar      $subject
     * @param ProductCollection $collection
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetCollection($subject, $collection)
    {
        if ($collection instanceof ProductCollection && !$this->isFulltextCollection($collection)) {
            // no image sorting will be the first or the second (after stock). LIFO queue
            $this->imageMethod->apply($collection);
            // in stock sorting will be first, as the method always moves it's paremater first. LIFO queue
            $this->stockMethod->apply($collection);
            $this->applyGlobalSorting->execute($collection);
        }

        return [$collection];
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $result
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function afterSetCollection($subject, $result)
    {
        $collection = $subject->getCollection();

        if ($collection instanceof ProductCollection) {
            $this->applyOrdersFromConfig($collection);
        }

        return $result;
    }

    private function applyOrdersFromConfig(ProductCollection $collection): void
    {
        if ($this->registry->registry(Data::SEARCH_SORTING)) {
            $defaultSortings = $this->helper->getSearchSorting();
        } else {
            $defaultSortings = $this->helper->getCategorySorting();
        }
        // first sorting must be setting by magento as default sorting
        array_shift($defaultSortings);

        foreach ($defaultSortings as $defaultSorting) {
            if ($this->isAvailableForSorting->execute($defaultSorting)) {
                $dir = $this->getDefaultDirection->execute($defaultSorting);
                $collection->setOrder($defaultSorting, $dir);
            }
        }
    }

    private function isFulltextCollection(ProductCollection $collection): bool
    {
        return $collection instanceof \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
            || $collection instanceof \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection
            || $collection instanceof \Amasty\ShopbyLite\Model\ResourceModel\Fulltext\Collection;
    }
}
