<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\CatalogGraphQl\DataProvider\Product\SearchCriteriaBuilder;

use Amasty\Sorting\Model\MethodProvider;
use Amasty\SortingGraphQl\Model\MethodProvider\CodeMap;
use Magento\CatalogGraphQl\DataProvider\Product\SearchCriteriaBuilder;

class FixSortOrderCode
{
    /**
     * @var MethodProvider
     */
    private $methodProvider;

    /**
     * @var CodeMap
     */
    private $codeMap;

    public function __construct(MethodProvider $methodProvider, CodeMap $codeMap)
    {
        $this->methodProvider = $methodProvider;
        $this->codeMap = $codeMap;
    }

    /**
     * Replace sort code with method alias(which used in catalogsearch_fulltext indexation) for elastic.
     * @see SearchCriteriaBuilder::build
     *
     * @param SearchCriteriaBuilder $subject
     * @param array $args
     * @param bool $includeAggregation
     * @return array
     */
    public function beforeBuild(SearchCriteriaBuilder $subject, array $args, bool $includeAggregation): array
    {
        if (isset($args['sort'])) {
            foreach ($args['sort'] as $sortCode => $direction) {
                $method = $this->methodProvider->getMethodByCode($sortCode);
                unset($args['sort'][$sortCode]);
                if ($method) {
                    $alias = $method->getAlias();
                    $args['sort'][$alias] = $direction;
                    $this->codeMap->set($sortCode, $alias);
                } else {
                    $args['sort'][$sortCode] = $direction;
                }
            }
        }

        return [$args, $includeAggregation];
    }
}
