<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;

class LoadSkuMap
{
    private const ENTITY_ID_FIELD = 'entity_id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $entityIds
     * @return array
     */
    public function execute(array $entityIds): array
    {
        $select = $this->resourceConnection->getConnection()->select()->from(
            $this->resourceConnection->getTableName('catalog_product_entity'),
            [self::ENTITY_ID_FIELD, ProductInterface::SKU]
        )->where(
            sprintf('%s IN (?)', self::ENTITY_ID_FIELD),
            $entityIds
        );

        return $this->resourceConnection->getConnection()->fetchPairs($select);
    }
}
