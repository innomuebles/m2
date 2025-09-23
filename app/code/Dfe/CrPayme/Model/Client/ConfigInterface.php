<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Dfe\CrPayme\Model\Client;

interface ConfigInterface
{
    function setConfig();

    function getConfig($key);
}
