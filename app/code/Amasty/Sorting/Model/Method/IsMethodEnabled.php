<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Model\ConfigProvider;

class IsMethodEnabled
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var IsMethodDisabledByConfig
     */
    private $isMethodDisabledByConfig;

    public function __construct(
        IsMethodDisabledByConfig $isMethodDisabledByConfig,
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
        $this->isMethodDisabledByConfig = $isMethodDisabledByConfig;
    }

    /**
     * Check is method enabled for indexation & applying.
     *
     * @param string $methodCode
     * @param int|null $storeId
     * @return bool
     */
    public function execute(string $methodCode, ?int $storeId = null): bool
    {
        return !$this->isMethodDisabledByConfig->execute($methodCode, $storeId)
            || in_array($methodCode, $this->configProvider->getDefaultSortingSearchPages($storeId))
            || in_array($methodCode, $this->configProvider->getDefaultSortingCategoryPages($storeId))
            || $methodCode === $this->configProvider->getGlobalSorting();
    }
}
