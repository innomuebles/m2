<?php
/**
 * Copyright © 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageBig\Shopbybrand\Helper;

use MageBig\Shopbybrand\Model\BrandFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogInventory\Helper\Stock;

class Data extends AbstractHelper
{
    protected $_objectManager;
    protected $_scopeConfig;
    protected $_urlBuilder;
    protected $_imageHelper;
    protected $_brandFactory;
    protected $_storeManager;

    protected $_brandProducts = [];
    protected $_brandProductCount = [];
    protected $_blockFilter;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Stock
     */
    private $stockHelper;

    /**
     * @param Context $context
     * @param Image $imageHelper
     * @param BrandFactory $brandFactory
     * @param StoreManagerInterface $storeManager
     * @param Configuration $configuration
     * @param Stock $stockHelper
     */

    public function __construct(
        Context               $context,
        Image                 $imageHelper,
        BrandFactory          $brandFactory,
        StoreManagerInterface $storeManager,
        Configuration         $configuration,
        Stock                 $stockHelper
    )
    {
        parent::__construct($context);
        $this->_objectManager = ObjectManager::getInstance();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_imageHelper = $imageHelper;
        $this->_brandFactory = $brandFactory;
        $this->_storeManager = $storeManager;
        $this->configuration = $configuration;
        $this->stockHelper = $stockHelper;
    }

    public function getStoreBrandCode()
    {
        return $this->_scopeConfig->getValue('magebig_shopbybrand/general/attribute_code');
    }

    public function getBrandRoute()
    {
        return $this->_scopeConfig->getValue('magebig_shopbybrand/general/brand_route', ScopeInterface::SCOPE_STORE);
    }

    public function getUrl($path, $params = [])
    {
        return $this->_urlBuilder->getUrl($path, $params);
    }

    public function getBrandImage($brand, $type, $options)
    {
        $brandThumb = $brand->getData($type);
        if ($brandThumb) {
            if (isset($options['width']) || isset($options['height'])) {
                if (!isset($options['width'])) {
                    $options['width'] = null;
                }
                if (!isset($options['height'])) {
                    $options['height'] = null;
                }
                return $this->_imageHelper->init($brandThumb)
                    ->resize($options['width'], $options['height'])->__toString();
            } else {
                return $this->_mediaUrl . $brand->getData($type);
            }
        } else {
            $imageHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);

            return $imageHelper->getDefaultPlaceholderUrl('small_image');
        }
    }

    public function getBrandPageUrl($brandModel)
    {
        $brandRoute = $this->getBrandRoute();
        if ($brandModel->getData('mb_brand_url_key')) {
            return $this->getUrl($brandRoute, ['_nosid' => true]) . $brandModel->getData('mb_brand_url_key');
        } else {
            return $this->getUrl($brandRoute, ['_nosid' => true]) . urlencode(str_replace([' ', "'"], ['-', '-'], strtolower(trim($brandModel->getData('brand_label')))));
        }
    }

    public function getProductCount($attributeCode, $optionId)
    {
        $key = $attributeCode . '_' . $optionId;
        if (!isset($this->_brandProductCount[$key])) {
            $collection = $this->_objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create();
            $collection->addAttributeToSelect([$attributeCode])
                ->addAttributeToSelect('status')
                ->addAttributeToSelect('visibility');
            $collection->addAttributeToFilter($attributeCode, $optionId);
            $collection->addAttributeToFilter('status', 1);
            $collection->addAttributeToFilter('visibility', ['in' => [2, 4]]);

            if ($this->configuration->isShowOutOfStock() != 1) {
                $this->stockHelper->addInStockFilterToCollection($collection);
            }

            $this->_brandProductCount[$key] = $collection->count();
        }

        return $this->_brandProductCount[$key];
    }

    public function getBrandByProduct($product, $attributeCode)
    {
        $attrValue = (int)$product->getData($attributeCode);

        if (!isset($this->_brandProducts[$attrValue])) {
            $brandModel = $this->_brandFactory->create()->setStoreId($this->_storeManager->getStore()->getId())
                ->setOptionId($attrValue)
                ->load(null);
            $brandModel->setUrl($this->getBrandPageUrl($brandModel));
            $brandModel->setThumbnail($this->getBrandImage($brandModel, 'mb_brand_thumbnail', ['width' => 150]));
            $this->_brandProducts[$attrValue] = $brandModel;
        }
        return $this->_brandProducts[$attrValue];
    }

    public function getBlockFilter()
    {
        if ($this->_blockFilter === null) {
            $this->_blockFilter = $this->_objectManager->get('Magento\Cms\Model\Template\FilterProvider')->getBlockFilter();
        }
        return $this->_blockFilter;
    }

    public function htmlFilter($content)
    {
        return $this->getBlockFilter()->filter($content);
    }
}
