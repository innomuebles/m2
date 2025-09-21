<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Controller\ConfigurableProduct;

use Magento\Catalog\Model\Product\Visibility;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magefan\GoogleTagManager\Api\DataLayer\ViewItemInterface;

class Identifiers extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ViewItemInterface
     */
    private $viewItem;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param Context $context
     * @param Config $config
     * @param ProductRepository $productRepository
     * @param JsonFactory $resultJsonFactory
     * @param ViewItemInterface $viewItem
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        Context $context,
        Config $config,
        ProductRepository $productRepository,
        JsonFactory $resultJsonFactory,
        ViewItemInterface $viewItem,
        ModuleManager $moduleManager
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->viewItem = $viewItem;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->config->isEnabled()) {
            return $result->setData(['success' => false, 'message' => 'Extension is disabled']);
        }

        $productId = (int) $this->getRequest()->getParam('product_id');
        $parentProductId = (int) $this->getRequest()->getParam('parent_product_id');
        if (!$productId || !$parentProductId) {
            return $result->setData(['success' => false, 'message' => 'Missing product id']);
        }
        try {
            return $result->setData(['success' => true, 'childProductDataLayer' => $this->getChildProductDataJson($productId, $parentProductId)]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param int $productId
     * @param int $parentProductId
     * @return string
     * @throws NoSuchEntityException
     */
    private function getChildProductDataJson(int $productId, int $parentProductId): string
    {
        $result = [];

        try {
            $childProduct = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return json_encode($result);
        }

        try {
            $parentProduct = $this->productRepository->getById($parentProductId);
        } catch (NoSuchEntityException $e) {
            return json_encode($result);
        }

        if ('configurable' != $parentProduct->getTypeId()) {
            return json_encode($result);
        }

        $data = $this->viewItem->get($childProduct);
        if ($childProduct->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE &&
            isset($data['ecommerce']['items'][0]['item_url'])) {
            $childUrl = $parentProduct->getProductUrl();
            if ($this->moduleManager->isEnabled('Magefan_GoogleShoppingFeed') || $this->moduleManager->isEnabled('Magefan_RichSnippets')) {
                $delimiter = (false === strpos($childUrl, '?')) ? '?' : '&';
                $childUrl .= $delimiter . 'mfpreselect=' . $childProduct->getId();
            }

            $attributes = $parentProduct->getTypeInstance()->getConfigurableAttributes($parentProduct);
            foreach ($attributes as $attribute) {
                $attrCode = $attribute->getProductAttribute()->getAttributeCode();
                $value = $childProduct->getData($attrCode);
                $delimiter = (false === strpos($childUrl, '?')) ? '?' : '&';
                $childUrl .= $delimiter . $attrCode . '=' . $value;
            }

            $data['ecommerce']['items'][0]['item_url'] = $childUrl;
        }
        $result = $data;

        $json = json_encode($result);
        $json = preg_replace('/"((getMfGtmCustomerData\(\)\.[^"]+))"/', '$1', $json);

        return $json;
    }

}