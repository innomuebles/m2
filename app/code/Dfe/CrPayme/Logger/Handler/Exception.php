<?php
/**
 *
 */

namespace Dfe\CrPayme\Logger\Handler;

use Monolog\Logger;

class Exception extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/Alignet/paymecheckout/exception.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::CRITICAL;
}
