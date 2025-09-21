<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Plugin\Catalog\Config;

/**
 * @see \Amasty\Sorting\Plugin\Catalog\Config
 * When flag set as true - all options must be displayed by plugin
 */
class DisplayAllFlag
{
    /**
     * @var bool
     */
    private $flag = false;

    /**
     * @param bool $flag
     * @return void
     */
    public function set(bool $flag): void
    {
        $this->flag = $flag;
    }

    /**
     * @return bool
     */
    public function get(): bool
    {
        return $this->flag;
    }
}
