<?php
/**
 * Copyright © 2020 MageBig, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageBig\Shopbybrand\Block\Widget;

use MageBig\Shopbybrand\Helper\Data;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\CollectionFactory;
use MageBig\Shopbybrand\Model\ResourceModel\BrandEntity\CollectionFactory as BrandFactory;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Store;

class BrandAbstract extends Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_brandFactory;
    protected $_brandObject;
    protected $_mediaUrl;
    protected $_objectManager;
    protected $_context;
    protected $_attributeCode;
    protected $_assetRepository;
    protected $_imageHelper;
    protected $_cacheTag = 'MAGEBIG_BRAND';
    protected $_template = '';
    protected $_categoryHeper = null;
    protected $_categoryRepository = null;
    protected $_coreRegistry = null;
    protected $_copeConfig;
    protected $_helper;
    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var Json
     */
    protected $_serialize;

    protected $dataCollectionFactory;
    protected $resource;

    public function __construct(
        Template\Context   $context,
        BrandFactory       $brandFactory,
        Context            $httpContext,
        Registry           $coreRegistry,
        Data               $helper,
        CollectionFactory  $dataCollectionFactory,
        ResourceConnection $resource,
        Json               $serialize,
        array              $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_brandFactory = $brandFactory;
        $this->httpContext = $httpContext;
        $this->_context = $context;
        $this->_storeManager = $context->getStoreManager();
        $this->_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_assetRepository = $context->getAssetRepository();
        $this->_helper = $helper;
        $this->_coreRegistry = $coreRegistry;
        $this->_copeConfig = $context->getScopeConfig();
        $this->_attributeCode = $helper->getStoreBrandCode();
        $this->_serialize = $serialize;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->resource = $resource;

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [$this->_cacheTag]
        ]);
    }

    public function getConfigValue($path)
    {
        return $this->_copeConfig->getValue($path);
    }

    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }

    public function getBrands($pageSize = 0, $offset = 0, $orderBy = 'brand_label', $order = 'asc', $isFeatured = 0)
    {
        $registryName = 'mb_brand_' . $orderBy . '_' . $order . '_' . $isFeatured . $pageSize . $offset;
        $collection = $this->_coreRegistry->registry($registryName);

        if (!$collection) {
            $collection = $this->dataCollectionFactory->create();
            $connection = $this->resource->getConnection();

            $defaultStoreId = Store::DEFAULT_STORE_ID;
            $storeId = $this->_storeManager->getStore()->getId();

            $select = $connection->select();
            $select->from(['main_table' => $this->resource->getTableName('eav_attribute_option')], ['option_id', 'sort_order'])
                ->joinLeft(['cea' => $this->resource->getTableName('catalog_eav_attribute')], 'main_table.attribute_id = cea.attribute_id', ['attribute_id'])
                ->joinLeft(['ea' => $this->resource->getTableName('eav_attribute')], 'cea.attribute_id = ea.attribute_id', ['attribute_code'])
                ->joinLeft(['eaov' => $this->resource->getTableName('eav_attribute_option_value')], 'eaov.option_id = main_table.option_id', ['store_id', 'default_brand_label' => 'eaov.value'])
                ->joinLeft(['eaov2' => $this->resource->getTableName('eav_attribute_option_value')], "eaov2.option_id = main_table.option_id AND eaov2.store_id = {$storeId}", ['brand_label' => 'IF(eaov2.value_id > 0, eaov2.value, eaov.value)'])
                ->where("ea.attribute_code = '{$this->_attributeCode}'")
                ->where("eaov.store_id = {$defaultStoreId}")
                ->order($orderBy . ' ' . $order);

            if ($pageSize) {
                $select->limit($pageSize, $offset);
            }

            if ($isFeatured) {
                $select->limit(6);
            }

            $rows = $connection->fetchAll($select);

            if (count($rows) > 0) {
                $optionIds = array_column($rows, 'option_id');
                $brandCollection = $this->_brandFactory->create()
                    ->setStore($storeId)
                    ->addFieldToFilter('option_id', ['in' => $optionIds])
                    ->addAttributeToSelect(['mb_brand_thumbnail', 'mb_brand_url_key', 'mb_brand_is_featured']);

                $brandItems = $brandCollection->getItems();
                $brands = [];
                foreach ($brandItems as $brandItem) {
                    $brands[$brandItem->getData('option_id')] = $brandItem;
                }
                foreach ($rows as $row) {
                    $optionId = $row['option_id'];
                    $row['product_count'] = $this->_helper->getProductCount($this->_attributeCode, $optionId);
                    if (isset($brands[$optionId])) {
                        if ($isFeatured && (!$brands[$optionId]->getData('mb_brand_is_featured'))) {
                            continue;
                        }

                        if (!$brands[$optionId]->getData('is_active')) {
                            continue;
                        }
                        $brandModel = $brands[$optionId]->addData($row);

                    } else {
                        $brandModel = new \Magento\Framework\DataObject($row);
                    }
                    $brandModel->setId($optionId);
                    $brandModel->setUrl($this->_helper->getBrandPageUrl($brandModel));
                    $collection->addItem($brandModel);
                }
            }

            if ($collection) {
                $this->_coreRegistry->register($registryName, $collection);
            }
        }

        return $collection;
    }

    public function getThumbnailImage($brand, array $options = [])
    {
        return $this->_helper->getBrandImage($brand, 'mb_brand_thumbnail', $options);
    }

    public function getBrandPageUrl($brandModel)
    {
        return $this->_helper->getBrandPageUrl($brandModel);
    }

    public function getCacheKeyInfo()
    {
        return [
            $this->_cacheTag,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $this->getTemplate()
        ];
    }

    public function getIdentities()
    {
        return [$this->_cacheTag . '_' . $this->getTemplate()];
    }
}
