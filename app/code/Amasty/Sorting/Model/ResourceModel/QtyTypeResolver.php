<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


declare(strict_types=1);

namespace Amasty\Sorting\Model\ResourceModel;

use Amasty\Sorting\Model\Di\Wrapper;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryBundleProductIndexer\Indexer\SelectBuilder as BundleSelectBuilder;
use Magento\InventoryConfigurableProductIndexer\Indexer\SelectBuilder as ConfigurableSelectBuilder;
use Magento\InventoryGroupedProductIndexer\Indexer\SelectBuilder as GroupedSelectBuilder;
use Magento\InventoryIndexer\Indexer\SelectBuilder as SimpleSelectBuilder;

/**
 * Resolve stock quantity for complex product types.
 * SUM all simple products qty of complex product.
 */
class QtyTypeResolver
{
    protected const DEFAULT_SKU_KEY = 'parent_product_entity.sku';

    /**
     * @var \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $selectBuildersNames;

    /**
     * @var array
     */
    private $skuFields;

    public function __construct(
        Wrapper $getProductTypesBySkus,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        $selectBuildersNames = [],
        $skuFields = []
    ) {
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->objectManager = $objectManager;
        $this->resourceConnection = $resourceConnection;
        $this->selectBuildersNames = $selectBuildersNames;
        $this->skuFields = $skuFields;
    }

    /**
     * @param int $stockId
     * @param array $skuList
     * @return array (key: 'sku', value: 'qty')
     */
    public function getQtyBySku(int $stockId, array $skuList)
    {
        $result = [];
        $skuTypes = [];

        foreach ($this->getProductTypesBySkus->execute($skuList) as $sku => $type) {
            $skuTypes[$type][] = $sku;
        }

        foreach ($skuTypes as $type => $skuListOfOneType) {
            $result += $this->getQtyByType($type, $stockId, $skuListOfOneType);
        }

        return $result;
    }

    /**
     * @param string $typeId
     * @param int $stockId
     * @param array $skuList
     *
     * @return array (key: 'sku', value: 'qty')
     */
    private function getQtyByType(string $typeId, int $stockId, array $skuList): array
    {
        $selectBuilder = $this->getSelectBuilderByType($typeId);
        $select = $selectBuilder->execute($stockId);
        $select->where($this->getSkuKey($typeId) . ' IN (?)', $skuList);

        return $this->resourceConnection->getConnection()->fetchPairs($select);
    }

    private function getSkuKey(string $typeId): string
    {
        return $this->skuFields[$this->getBuilderClassName($typeId)] ?? static::DEFAULT_SKU_KEY;
    }

    /**
     * @param string $typeId
     *
     * @return SimpleSelectBuilder|ConfigurableSelectBuilder|BundleSelectBuilder|GroupedSelectBuilder
     */
    private function getSelectBuilderByType(string $typeId)
    {
        return $this->objectManager->get($this->getBuilderClassName($typeId));
    }

    private function getBuilderClassName(string $typeId): string
    {
        return $this->selectBuildersNames[$typeId] ?? SimpleSelectBuilder::class;
    }
}
