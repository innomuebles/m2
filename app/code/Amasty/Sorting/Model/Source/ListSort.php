<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Source;

use Magento\Catalog\Model\Config\Source\ListSort as NativeListSort;

class ListSort extends NativeListSort
{
    public const IGNORE_ATTRIBUTES = [
        'price_asc',
        'price_desc'
    ];

    /**
     * @var bool
     */
    private $enabledIgnoreAttributes = true;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        foreach ($options as $key => $option) {
            if (isset($option['value'])
                && $this->enabledIgnoreAttributes
                && in_array($option['value'], self::IGNORE_ATTRIBUTES)
            ) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    public function getAllOptions(): array
    {
        $this->enabledIgnoreAttributes = false;
        $options = $this->toOptionArray();
        $this->enabledIgnoreAttributes = true;

        return $options;
    }
}
