<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Method;

use Amasty\Sorting\Model\ConfigProvider;

class IsMethodDisabledByConfig
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * Check is method disabled.
     *
     * @param string $methodCode
     * @param int|null $storeId
     * @return bool
     */
    public function execute(string $methodCode, ?int $storeId = null): bool
    {
        $result = false;
        foreach ($this->configProvider->getDisabledMethods($storeId) as $disabledCode) {
            if ($disabledCode === $methodCode) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
