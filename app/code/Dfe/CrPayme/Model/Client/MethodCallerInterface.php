<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client;

interface MethodCallerInterface
{
    /**
     * @param string $methodName
     * @param array $args
     * @return mixed
     */
    function call($methodName, array $args = []);
}
