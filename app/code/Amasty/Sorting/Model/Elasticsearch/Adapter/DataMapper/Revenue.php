<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Elasticsearch\Adapter\DataMapper;

use Amasty\Sorting\Model\Elasticsearch\Adapter\IndexedDataMapper;

class Revenue extends IndexedDataMapper
{
    public const FIELD_NAME = 'revenue';

    public function getIndexerCode(): string
    {
        return 'amasty_sorting_revenue';
    }
}
