<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model;

use Amasty\Base\Model\Serializer;
use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    public const DEFAULT_SORTING_SECTION = 'default_sorting';

    public const DEFAULT_SORTING_SEARCH_PAGES_1 = 'search_1';
    public const DEFAULT_SORTING_SEARCH_PAGES_2 = 'search_2';
    public const DEFAULT_SORTING_SEARCH_PAGES_3 = 'search_3';

    public const DEFAULT_SORTING_CATEGORY_PAGES_1 = 'category_1';
    public const DEFAULT_SORTING_CATEGORY_PAGES_2 = 'category_2';
    public const DEFAULT_SORTING_CATEGORY_PAGES_3 = 'category_3';

    public const DISABLED_METHODS_PATH = 'general/disable_methods';
    public const CONFIG_SORT_ORDER = 'general/sort_order';
    public const NON_IMAGES_LAST_ENABLED_PATH = 'general/no_image_last';
    public const OUT_OF_STOCK_LAST_ENABLED_PATH = 'general/out_of_stock_last';
    public const OUT_OF_STOCK_BY_QTY_PATH = 'general/out_of_stock_qty';

    public const GLOBAL_SORTING_PATH = 'advanced/global';
    public const GLOBAL_SORTING_DIRECTION_PATH = 'advanced/global_direction';

    public const BESTSELLER_ATTRIBUTE_CODE_PATH = 'bestsellers/best_attr';
    private const BESTSELLER_EXCLUDE_PATH = 'bestsellers/exclude';
    private const BESTSELLER_PERIOD_PATH = 'bestsellers/best_period';

    public const MOSTVIEWED_ATTRIBUTE_CODE_PATH = 'most_viewed/viewed_attr';
    public const NEW_ATTRIBUTE_CODE_PATH = 'new/new_attr';

    private const REVENUE_LABEL_PATH = 'revenue/label';
    private const REVENUE_ATTRIBUTE_CODE_PATH = 'revenue/attr';
    private const REVENUE_PERIOD_PATH = 'revenue/period';
    private const REVENUE_EXCLUDE_PATH = 'revenue/exclude';

    /**
     * @var string
     */
    protected $pathPrefix = 'amsorting/';

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer
    ) {
        parent::__construct($scopeConfig);
        $this->serializer = $serializer;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getDefaultSortingSearchPages(?int $storeId = null): array
    {
        $paths = [
            self::DEFAULT_SORTING_SEARCH_PAGES_1,
            self::DEFAULT_SORTING_SEARCH_PAGES_2,
            self::DEFAULT_SORTING_SEARCH_PAGES_3
        ];

        return $this->getDefaultOrders($paths, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getDefaultSortingCategoryPages(?int $storeId = null): array
    {
        $paths = [
            self::DEFAULT_SORTING_CATEGORY_PAGES_1,
            self::DEFAULT_SORTING_CATEGORY_PAGES_2,
            self::DEFAULT_SORTING_CATEGORY_PAGES_3
        ];

        return $this->getDefaultOrders($paths, $storeId);
    }

    /**
     * @return array
     */
    public function getSortOrder(): array
    {
        $value = $this->getValue(self::CONFIG_SORT_ORDER);
        if ($value) {
            $value = $this->serializer->unserialize($value);
        }
        if (!$value) {
            $value = [];
        }

        return $value;
    }

    private function getDefaultOrders(array $paths, ?int $storeId = null): array
    {
        $defaultOrders = [];
        foreach ($paths as $path) {
            $orderCode = $this->getValue(
                sprintf('%s/%s', self::DEFAULT_SORTING_SECTION, $path),
                $storeId
            );
            if ($orderCode) {
                $defaultOrders[] = $orderCode;
            }
        }

        return $defaultOrders;
    }

    /**
     * @param null|int $storeId
     * @return string[]
     */
    public function getDisabledMethods(?int $storeId = null): array
    {
        $disabledMethods = $this->getValue(self::DISABLED_METHODS_PATH, $storeId);
        if (empty($disabledMethods)) {
            $disabledMethods = [];
        } else {
            $disabledMethods = explode(',', $disabledMethods);
        }

        return $disabledMethods;
    }

    /**
     * @return string|null
     */
    public function getGlobalSorting(): ?string
    {
        return $this->getValue(self::GLOBAL_SORTING_PATH);
    }

    /**
     * @return string
     */
    public function getGlobalSortingDirection(): string
    {
        return (string) $this->getValue(self::GLOBAL_SORTING_DIRECTION_PATH);
    }

    public function getBestsellerAttributeCode(): string
    {
        return (string) $this->getValue(self::BESTSELLER_ATTRIBUTE_CODE_PATH);
    }

    public function getMostviewedAttributeCode(): ?string
    {
        return $this->getValue(self::MOSTVIEWED_ATTRIBUTE_CODE_PATH);
    }

    public function getNewAttributeCode(): ?string
    {
        return $this->getValue(self::NEW_ATTRIBUTE_CODE_PATH);
    }

    /**
     * @return string
     */
    public function getRevenueAttributeCode(): string
    {
        return (string)$this->getValue(self::REVENUE_ATTRIBUTE_CODE_PATH);
    }

    /**
     * @return string
     */
    public function getRevenueLabel(): string
    {
        return (string)$this->getValue(self::REVENUE_LABEL_PATH);
    }

    /**
     * @return int
     */
    public function getRevenuePeriod(): int
    {
        return (int)$this->getValue(self::REVENUE_PERIOD_PATH);
    }

    /**
     * @return string
     */
    public function getExcludedOrderStatusesForRevenue(): array
    {
        $excludedStatuses = (string)$this->getValue(self::REVENUE_EXCLUDE_PATH);

        return explode(',', $excludedStatuses);
    }

    /**
     * @return array
     */
    public function getExcludedOrderStatusesForBestsellers(): array
    {
        $excludedStatuses = (string)$this->getValue(self::BESTSELLER_EXCLUDE_PATH);

        return explode(',', $excludedStatuses);
    }

    /**
     * @return int
     */
    public function getBestsellersPeriod(): int
    {
        return (int)$this->getValue(self::BESTSELLER_PERIOD_PATH);
    }

    public function isNonImagesLastEnabled(?int $storeId): bool
    {
        return $this->isSetFlag(self::NON_IMAGES_LAST_ENABLED_PATH, $storeId);
    }

    public function isOutOfStockLastEnabled(?int $storeId): bool
    {
        return $this->isSetFlag(self::OUT_OF_STOCK_LAST_ENABLED_PATH, $storeId);
    }

    public function isOutOfStockByQty(?int $storeId): bool
    {
        return $this->isSetFlag(self::OUT_OF_STOCK_BY_QTY_PATH, $storeId);
    }

    public function getQtyOutStock(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            Configuration::XML_PATH_MIN_QTY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
