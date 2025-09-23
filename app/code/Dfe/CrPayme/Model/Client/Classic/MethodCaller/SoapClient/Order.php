<?php
/**
 *
 */

namespace Dfe\CrPayme\Model\Client\Classic\MethodCaller\SoapClient;

class Order extends \Zend\Soap\Client
{
    /**
     * @var int
     */
    protected $soapVersion = SOAP_1_1;
}
