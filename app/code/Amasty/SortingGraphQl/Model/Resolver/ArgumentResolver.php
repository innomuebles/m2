<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Model\Resolver;

class ArgumentResolver
{
    /**
     * @param array $args
     * @return array
     */
    public function convertArgs(array $args): array
    {
        $result = [];
        foreach ($args as $key => $arg) {
            $result[$this->convertFromCamelCase($key)] = $arg;
        }

        return $result;
    }

    private function convertFromCamelCase(string $name): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }
}
