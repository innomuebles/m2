<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\MethodCaller;

use Magento\Framework\Exception\LocalizedException;

interface RawInterface
{
    /**
     * @param string $methodName
     * @param array $args
     * @return \stdClass
     * @throws LocalizedException
     */
    function call($methodName, array $args = []);
}
