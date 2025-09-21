<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\CatalogGraphQl\Model\Resolver\Products\Query\Search;

use Amasty\SortingGraphQl\Model\SearchPageFlag;
use Magento\CatalogGraphQl\Model\Resolver\Products\Query\Search;

class SetSearchFlag
{
    /**
     * @var SearchPageFlag
     */
    private $searchPageFlag;

    public function __construct(SearchPageFlag $searchPageFlag)
    {
        $this->searchPageFlag = $searchPageFlag;
    }

    /**
     * @param Search $subject
     * @param array $args
     * @return void
     */
    public function beforeGetResult(Search $subject, array $args): void
    {
        if (isset($args['search'])) {
            $this->searchPageFlag->set(true);
        }
    }
}
