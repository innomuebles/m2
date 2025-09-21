<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Source;

use Amasty\Sorting\Model\ConfigProvider;

class SortOptions
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function execute(array $options): array
    {
        if ($this->configProvider->getSortOrder()) {
            uksort($options, [$this, "sortingRule"]);
        }

        return $options;
    }

    private function sortingRule(string $leftItem, string $rightItem): int
    {
        $correctSortOrder = array_flip(array_keys($this->configProvider->getSortOrder()));
        $leftPos = $correctSortOrder[$leftItem] ?? 0;
        $rightPos = $correctSortOrder[$rightItem] ?? 0;

        return $leftPos <=> $rightPos;
    }
}
