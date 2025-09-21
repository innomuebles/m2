<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Source;

/**
 * Class Stock
 */
abstract class AbstractShowLast implements \Magento\Framework\Data\OptionSourceInterface
{
    public const DISABLED = 0;

    public const SHOW_LAST = 1;

    public const SHOW_LAST_FOR_CATALOG = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::DISABLED,
                'label' => __('No')
            ],
            [
                'value' => self::SHOW_LAST,
                'label' => __('Yes')
            ],
            [
                'value' => self::SHOW_LAST_FOR_CATALOG,
                'label' => __('Yes for Catalog, No for Search')
            ]
        ];

        return $options;
    }
}
