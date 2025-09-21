<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\MethodProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ApplyGlobalSorting
{
    private const FLAG_NAME = 'global_sorting';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var MethodProvider
     */
    private $methodProvider;

    /**
     * @var IsAvailableForSorting
     */
    private $isAvailableForSorting;

    public function __construct(
        ConfigProvider $configProvider,
        MethodProvider $methodProvider,
        IsAvailableForSorting $isAvailableForSorting
    ) {
        $this->configProvider = $configProvider;
        $this->methodProvider = $methodProvider;
        $this->isAvailableForSorting = $isAvailableForSorting;
    }

    /**
     * Apply global sorting if needed to collection.
     * Set flag in collection for prevent repeated applying.
     * Resolve custom amasty sorting methods with our plugin
     * For mysql needed instantly applying with addAttributeToSort.
     *
     * @see \Amasty\Sorting\Plugin\Catalog\Product\Collection::beforeSetOrder
     */
    public function execute(Collection $collection): void
    {
        $attribute = $this->configProvider->getGlobalSorting();
        if (!$attribute || !$this->isAvailableForSorting->execute($attribute)) {
            return;
        }

        if (!$collection->getFlag(self::FLAG_NAME)) {
            $direction = $this->configProvider->getGlobalSortingDirection();

            $collection->setOrder($attribute, $direction); // trigger resolving amasty sorting methods
            if ($method = $this->methodProvider->getMethodByCode($attribute)) {
                $attribute = $method->getAlias(); // resolve joined attribute for amasty sorting method
            }
            $collection->addAttributeToSort($attribute, $direction); // trigger instantly apply for mysql

            $collection->setFlag(self::FLAG_NAME, true);
        }
    }
}
