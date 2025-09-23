<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\Classic\MethodCaller;

use Dfe\CrPayme\Model\Client\MethodCaller\RawInterface;

class Raw implements RawInterface
{
    /**
     * @var SoapClient\Order
     */
    protected $orderClient;

    /**
     * @param SoapClient\Order $orderClient
     */
    function __construct(
        SoapClient\Order $orderClient
    ) {
        $this->orderClient = $orderClient;
    }

    /**
     * @inheritdoc
     */
    function call($methodName, array $args = [])
    {
        return call_user_func_array([$this, $methodName], $args);
    }

    /**
     * @param int $posId
     * @param string $sessionId
     * @param string $ts
     * @param string $sig
     * @return \stdClass
     * @throws \Exception
     */
    function orderRetrieve($posId, $sessionId, $ts, $sig)
    {
        return $this->orderClient->call('get', [
            'posId' => $posId,
            'sessionId' => $sessionId,
            'ts' => $ts,
            'sig' => $sig
        ]);
    }

}
