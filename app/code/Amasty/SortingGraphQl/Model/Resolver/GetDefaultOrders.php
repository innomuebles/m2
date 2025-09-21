<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Model\Resolver;

use Amasty\Sorting\Model\Catalog\Toolbar\GetDefaultDirection;
use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\Source\ListSort;
use Amasty\Sorting\Model\Source\SearchSort;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetDefaultOrders implements ResolverInterface
{
    public const CATEGORY_PAGE_TYPE = 'CATEGORY';

    public const PAGE_TYPE_PARAM = 'pageType';

    /**
     * @var ConfigProvider
     */
    private $config;

    /**
     * @var GetDefaultDirection
     */
    private $getDefaultDirection;

    /**
     * @var ListSort
     */
    private $listSort;

    /**
     * @var SearchSort
     */
    private $searchListSort;

    public function __construct(
        ConfigProvider $config,
        GetDefaultDirection $getDefaultDirection,
        ListSort $listSort,
        SearchSort $searchListSort
    ) {
        $this->config = $config;
        $this->getDefaultDirection = $getDefaultDirection;
        $this->listSort = $listSort;
        $this->searchListSort = $searchListSort;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        if ($args[self::PAGE_TYPE_PARAM] === self::CATEGORY_PAGE_TYPE) {
            $sortOrders = $this->config->getDefaultSortingCategoryPages();
            $labels = $this->getSortLabels($sortOrders, $this->listSort->getAllOptions());
        } else {
            $sortOrders = $this->config->getDefaultSortingSearchPages();
            $labels = $this->getSortLabels($sortOrders, $this->searchListSort->toOptionArray());
        }

        foreach ($sortOrders as $sortOrderCode) {
            if ($sortOrderCode) {
                $result[] = [
                    'attribute' => $sortOrderCode,
                    'id' => $sortOrderCode,
                    'text' => $labels[$sortOrderCode],
                    'sortDirection' => strtoupper($this->getDefaultDirection->execute($sortOrderCode))
                ];
            }
        }

        return $result;
    }

    private function getSortLabels(array $sortValues, array $sortOptions): array
    {
        $labels = [];
        foreach ($sortOptions as $sortOption) {
            if (in_array($sortOption['value'], $sortValues)) {
                $labels[$sortOption['value']] = $sortOption['label'];
            }
        }

        return $labels;
    }
}
