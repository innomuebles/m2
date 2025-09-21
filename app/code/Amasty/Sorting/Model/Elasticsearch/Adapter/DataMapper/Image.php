<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Helper\Data;
use Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapperInterface;

class Image implements DataMapperInterface
{
    /**
     * @var Data
     */
    private $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function map(int $entityId, array $entityIndexData, int $storeId, ?array $context = []): array
    {
        $value = isset($context['document']['small_image'])
            ? (int) ($context['document']['small_image'] !== 'no_selection')
            : 0;

        return ['non_images_last' => $value];
    }

    public function isAllowed(int $storeId): bool
    {
        return (bool) $this->data->getNonImageLast($storeId);
    }
}
