<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Elasticsearch\Model\Adapter\BatchDataMapper;

use Amasty\Sorting\Helper\Data;
use Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapperInterface;
use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;
use Amasty\Sorting\Model\Elasticsearch\SkuRegistry;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

class AdditionalProductDataMapper
{
    /**
     * @var SkuRegistry
     */
    private $skuRegistry;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var DataMapperInterface[]
     */
    private $dataMappers;

    public function __construct(
        SkuRegistry $skuRegistry,
        Data $helper,
        array $dataMappers = []
    ) {
        $this->skuRegistry = $skuRegistry;
        $this->helper = $helper;
        $this->dataMappers = $dataMappers;
    }

    /**
     * Prepare index data for using in search engine metadata.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ProductDataMapper $subject
     * @param callable $proceed
     * @param array $documentData
     * @param $storeId
     * @param array $context
     * @return array
     */
    public function afterMap(
        $subject,
        array $result,
        array $documentData,
        $storeId,
        $context = []
    ) {
        $productIds = array_keys($result);
        if ($this->helper->getOutOfStockLast($storeId)) {
            /** load sku relations needed in @see \Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper\Stock */
            $this->skuRegistry->save($productIds);
        }
        foreach ($result as $productId => $document) {
            $context['document'] = $document;
            foreach ($this->dataMappers as $mapper) {
                if ($mapper instanceof DataMapperInterface && $mapper->isAllowed($storeId)) {
                    if ($mapper instanceof IndexedDataMapper) {
                        $mapper->loadEntities($storeId, $productIds);
                    }
                    //@codingStandardsIgnoreLine
                    $document = array_merge($document, $mapper->map($productId, $document, $storeId, $context));
                }
            }
            $result[$productId] = $document;
        }
        $this->clearData($storeId);

        return $result;
    }

    private function clearData(int $storeId): void
    {
        $this->skuRegistry->clear();
        foreach ($this->dataMappers as $mapper) {
            if ($mapper instanceof IndexedDataMapper && $mapper->isAllowed($storeId)) {
                $mapper->clearValues();
            }
        }
    }
}
