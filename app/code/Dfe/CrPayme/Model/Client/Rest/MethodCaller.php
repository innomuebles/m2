<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\Rest;

class MethodCaller extends \Dfe\CrPayme\Model\Client\MethodCaller
{
    function __construct(
        MethodCaller\Raw $rawMethod,
        \Dfe\CrPayme\Logger\Logger $logger
    ) {
        parent::__construct(
            $rawMethod,
            $logger
        );
    }
}
