<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Model\Plugin\Catalog\Config\DisplayAllFlag;

class IsMethodDisplayed
{
    /**
     * @var DisplayAllFlag
     */
    private $displayAllFlag;

    /**
     * @var IsMethodDisabledByConfig
     */
    private $isMethodDisabledByConfig;

    public function __construct(
        DisplayAllFlag $displayAllFlag,
        IsMethodDisabledByConfig $isMethodDisabledByConfig
    ) {
        $this->displayAllFlag = $displayAllFlag;
        $this->isMethodDisabledByConfig = $isMethodDisabledByConfig;
    }

    /**
     * @param string $methodCode
     * @return bool
     */
    public function execute(string $methodCode): bool
    {
        return $this->displayAllFlag->get()
            || !$this->isMethodDisabledByConfig->execute($methodCode);
    }
}
