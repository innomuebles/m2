<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Catalog\Toolbar;

use Amasty\Sorting\Helper\Data;

class GetDefaultDirection
{
    public const ALWAYS_DESC = [
        'price_desc'
    ];

    public const ALWAYS_ASC = [
        'price_asc'
    ];

    public const RELEVANCE_ORDER = 'relevance';

    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param string $order
     * @return string
     */
    public function execute(string $order): string
    {
        return  $this->isDescDirection($order) ? 'desc' : 'asc';
    }

    private function isDescDirection(string $order): bool
    {
        $attributeCodes = $this->helper->getScopeValue('general/desc_attributes');
        $shouldBeDesc = array_merge(self::ALWAYS_DESC, [self::RELEVANCE_ORDER]);

        if ($attributeCodes) {
            $shouldBeDesc = array_merge($shouldBeDesc, explode(',', $attributeCodes));
        }

        return in_array($order, $shouldBeDesc);
    }
}
