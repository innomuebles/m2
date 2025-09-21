<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class FeaturedList implements ResolverInterface
{

    public const AM_FEATURED_WIDGET = 'am_featured_widget';
    /**
     * @var \Amasty\Sorting\Block\Widget\Featured
     */
    private $featured;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    /**
     * @var ArgumentResolver
     */
    private $argumentResolver;

    public function __construct(
        \Amasty\Sorting\Block\Widget\Featured $featured,
        \Magento\Catalog\Helper\Product\Compare $compareHelper,
        ArgumentResolver $argumentResolver
    ) {
        $this->featured = $featured;
        $this->compareHelper = $compareHelper;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();

        try {
            $data = $this->argumentResolver->convertArgs($args);
            $this->setFeaturedData($data, $storeId);
        } catch (\Exception $e) {
            return [];
        }

        $data = [];
        $productCollection = $this->featured->createCollection()->setFlag(self::AM_FEATURED_WIDGET, true);
        $productCollection->setCurPage($data['current_page'] ?? 1);
        foreach ($productCollection as $product) {
            $data[] = $this->prepareData($product, $context);
        }

        return $data;
    }

    /**
     * @param array $data
     * @param int $storeId
     * @return void
     * @throws \Exception
     */
    private function setFeaturedData(array $data, int $storeId): void
    {
        $this->featured->setNameInLayout('amsorting_featured_list');
        $this->featured->setData($data);
        $this->featured->setData('store_id', $storeId);
    }

    /**
     * @param $product
     * @param ContextInterface $context
     * @return array
     */
    private function prepareData($product, ContextInterface $context)
    {
        $data['id'] = $product->getEntityId();
        $data['productUrl'] = $this->getRelativePath($this->featured->getProductUrl($product), $context);
        $data['name'] = $product->getName();
        $data['isSalable'] = $product->isSaleable() || $product->getIsSalable();
        $data['hasRequiredOptions'] = $product->getTypeInstance()->hasRequiredOptions($product);
        $data['addToCartUrl'] = $this->featured->getAddToCartUrl($product);
        $data['addToCompareParams'] = $this->compareHelper->getPostDataParams($product);
        $data['sku'] = $product->getSku();
        $data['model'] = $product;

        return $data;
    }

    private function getRelativePath(string $url, ContextInterface $context): string
    {
        $baseUrl = trim($context->getExtensionAttributes()->getStore()->getBaseUrl(), '/');

        return str_replace($baseUrl, '', $url);
    }
}
