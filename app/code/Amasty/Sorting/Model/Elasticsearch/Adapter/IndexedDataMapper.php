<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Elasticsearch\Adapter;

use Amasty\Sorting\Model\Method\IsMethodEnabled;
use Amasty\Sorting\Model\ResourceModel\Method\AbstractMethod;

abstract class IndexedDataMapper implements DataMapperInterface
{
    public const DEFAULT_VALUE = 0;

    /**
     * @var AbstractMethod
     */
    protected $resourceMethod;

    /**
     * @var array|null
     */
    protected $values = null;

    /**
     * @var IsMethodEnabled
     */
    private $isMethodEnabled;

    public function __construct(
        AbstractMethod $resourceMethod,
        IsMethodEnabled $isMethodEnabled
    ) {
        $this->resourceMethod = $resourceMethod;
        $this->isMethodEnabled = $isMethodEnabled;
    }

    /**
     * @return string
     */
    abstract public function getIndexerCode();

    /**
     * @param int $storeId
     * @param array|null $entityIds
     * @return array
     */
    protected function forceLoad(int $storeId, ?array $entityIds = []): array
    {
        return $this->resourceMethod->getIndexedValues($storeId, $entityIds);
    }

    public function isAllowed(int $storeId): bool
    {
        return $this->isMethodEnabled->execute($this->resourceMethod->getMethodCode(), $storeId);
    }

    public function map(int $entityId, array $entityIndexData, int $storeId, ?array $context = []): array
    {
        $value = $this->values[$entityId] ?? self::DEFAULT_VALUE;

        return [static::FIELD_NAME => $value];
    }

    public function loadEntities(int $storeId, array $entityIds): void
    {
        if ($this->values === null) {
            $this->values = $this->forceLoad($storeId, $entityIds);
        }
    }

    public function clearValues(): void
    {
        $this->values = null;
    }
}
