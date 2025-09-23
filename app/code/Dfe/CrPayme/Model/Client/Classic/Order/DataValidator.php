<?php
/**
 * @copyright Copyright (c)  2019 Alignet  (https://www.pay-me.com)
 */

namespace Dfe\CrPayme\Model\Client\Classic\Order;

class DataValidator extends \Dfe\CrPayme\Model\Client\DataValidator
{
    /**
     * @var array
     */
    protected $requiredBasicKeys = [
        'idEntCommerce'
       
    ];

    /**
     * @param array $data
     * @return bool
     */
    function validateBasicData(array $data = [])
    {

        return true;
    }

    /**
     * @return array
     */
    protected function getRequiredBasicKeys()
    {
        return $this->requiredBasicKeys;
    }
}
