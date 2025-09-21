<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Plugin\Magento\Catalog\Block\Product;

use Magefan\GoogleTagManagerPlus\Api\DataLayer\ViewItemListInterface;
use Magefan\GoogleTagManagerPlus\Block\DataLayer\ViewItemList;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Block\Product\ProductList\Upsell;
use Magento\Checkout\Block\Cart\Crosssell;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;

class ListProductPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ViewItemListInterface
     */
    private $viewItemList;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * ListProductPlugin constructor.
     *
     * @param Config $config
     * @param ViewItemListInterface $viewItemList
     * @param RequestInterface|null $request
     * @param Registry|null $registry
     */
    public function __construct(
        Config $config,
        ViewItemListInterface $viewItemList,
        ?RequestInterface $request = null,
        ?Registry $registry = null
    ) {
        $this->config = $config;
        $this->viewItemList = $viewItemList;

        $this->request = $request ?: ObjectManager::getInstance()->get(
            RequestInterface::class
        );
        $this->registry = $registry ?: ObjectManager::getInstance()->get(
            Registry::class
        );
    }

    /**
     * Add datalayer to block's html output
     *
     * @phpcs:ignore
     * @param $subject
     * @param string $result
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function afterToHtml($subject, string $result): string
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (false !== strpos(get_class($subject), 'Codazon\ProductFilter')) {
            return $result;
        }

        if ($subject instanceof ListProduct) {
            $itemListName = 'Category products';
            if ('catalog_category_view' == $this->request->getFullActionName()) {
                if ($category = $this->registry->registry('current_category')) {
                    $itemListName = 'Category ' . $category->getName();
                }
            }
            $productItems = $subject->getLoadedProductCollection()->getItems();
            $this->viewItemList->setItemListName($itemListName);
        } elseif ($subject instanceof ProductsList) {
            $productItems = $subject->getProductCollection() ? $subject->getProductCollection()->getItems() : [];
            $this->viewItemList->setItemListName('Catalog Widget products');
        } else {
            $productItems = $subject->getItems();
            if (!is_array($productItems)) {
                $items = [];
                foreach ($productItems as $item) {
                    $items[] = $item;
                }
                $productItems = $items;
            }
            if ($subject instanceof Related) {
                $this->viewItemList->setItemListName('Related products');
            } elseif ($subject instanceof Crosssell) {
                $this->viewItemList->setItemListName('Cross-sell products');
            } elseif ($subject instanceof Upsell) {
                $this->viewItemList->setItemListName('Up-sell products');
            } elseif (strpos(get_class($subject), 'Magefan\Blog\Block\Post\View\RelatedProducts') !== false) {
                $this->viewItemList->setItemListName('Blog Related products');
            }
        }

        if (count($productItems)) {
            $dataLayer = $this->viewItemList->get($productItems);

            if ($dataLayer) {
                $block = $subject->getLayout()->createBlock(ViewItemList::class);
                $block->setItems($dataLayer);
                $position = strpos($result, 'class="');
                if ($position !== false) {
                    $position += 7;
                    $customClass = 'mfgtm-list-product-' . hash('sha256', $block->getNameInLayout()) . rand() .' ';
                    $result = substr_replace($result, $customClass, $position, 0);
                    $block->setSelector($customClass);
                    $result .= $block->toHtml();
                }
            }
        }

        return $result;
    }
}
