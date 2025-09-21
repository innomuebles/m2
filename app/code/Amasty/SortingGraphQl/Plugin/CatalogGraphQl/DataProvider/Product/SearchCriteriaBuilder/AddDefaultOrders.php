<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\CatalogGraphQl\DataProvider\Product\SearchCriteriaBuilder;

use Amasty\Sorting\Model\Catalog\Toolbar\GetDefaultDirection;
use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\IsSearchPage;
use Magento\CatalogGraphQl\DataProvider\Product\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;

class AddDefaultOrders
{
    /**
     * @var IsSearchPage
     */
    private $isSearchPage;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetDefaultDirection
     */
    private $getDefaultDirection;

    public function __construct(
        IsSearchPage $isSearchPage,
        ConfigProvider $configProvider,
        GetDefaultDirection $getDefaultDirection
    ) {
        $this->isSearchPage = $isSearchPage;
        $this->configProvider = $configProvider;
        $this->getDefaultDirection = $getDefaultDirection;
    }

    /**
     * Add default orders.
     * @see SearchCriteriaBuilder::build
     *
     * @param SearchCriteriaBuilder $subject
     * @param array $args
     * @param bool $includeAggregation
     * @return array
     */
    public function beforeBuild(SearchCriteriaBuilder $subject, array $args, bool $includeAggregation): array
    {
        $defaultOrders = $this->isSearchPage->execute()
            ? $this->configProvider->getDefaultSortingSearchPages()
            : $this->configProvider->getDefaultSortingCategoryPages();

        if (isset($args['sort'])) {
            // first order already in array
            array_shift($defaultOrders);
        } else {
            // if orders not passed , fill all with default
            $args['sort'] = [];
        }

        foreach ($defaultOrders as $defaultOrder) {
            $direction = $this->getDefaultDirection->execute($defaultOrder);
            $args['sort'][$defaultOrder] = $direction === 'desc' ? SortOrder::SORT_DESC : SortOrder::SORT_ASC;
        }

        return [$args, $includeAggregation];
    }
}
