<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Inventory;

use Amasty\Sorting\Model\ResourceModel\Inventory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

class GetConfigurableQty
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        Inventory $inventory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->inventory = $inventory;
    }

    /**
     * @param string $productSku
     * @param string $websiteCode
     * @return null|float
     */
    public function execute(string $productSku, string $websiteCode): ?float
    {
        $connection = $this->resourceConnection->getConnection();

        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();

        $select = $connection->select()->from(
            ['source_item' => $this->resourceConnection->getTableName('inventory_source_item')],
            ['SUM(quantity)']
        )->joinInner(
            ['product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')],
            'product_entity.sku = source_item.sku',
            []
        )->joinInner(
            ['parent_link' => $this->resourceConnection->getTableName('catalog_product_super_link')],
            'parent_link.product_id = product_entity.entity_id',
            []
        )->joinInner(
            ['parent_product_entity' => $this->resourceConnection->getTableName('catalog_product_entity')],
            'parent_product_entity.' . $linkField . ' = parent_link.parent_id',
            []
        )->where(
            'source_code IN (?)',
            $this->inventory->getSourceCodes($websiteCode)
        )->where(
            'parent_product_entity.sku = ?',
            $productSku
        )->group(
            'parent_product_entity.sku'
        );
        $qty = $connection->fetchOne($select);

        return $qty ? (float) $qty : null;
    }
}
