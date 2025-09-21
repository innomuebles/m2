<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Model;

class SearchPageFlag
{
    /**
     * @var bool
     */
    private $flag = false;

    public function set(bool $flag): void
    {
        $this->flag = $flag;
    }

    public function get(): bool
    {
        return $this->flag;
    }
}
