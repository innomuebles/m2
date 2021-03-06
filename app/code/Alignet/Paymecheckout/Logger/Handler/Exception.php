<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Alignet\Paymecheckout\Logger\Handler;

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
