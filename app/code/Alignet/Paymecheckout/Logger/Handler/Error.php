<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Alignet\Paymecheckout\Logger\Handler;

use Monolog\Logger;

class Error extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/Alignet/paymecheckout/error.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::ERROR;
}
