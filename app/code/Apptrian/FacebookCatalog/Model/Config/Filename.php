<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\FacebookCatalog\Model\Config;

use Magento\Framework\Exception\LocalizedException;

class Filename extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $validator = \Zend_Validate::is(
            $value,
            'Regex',
            ['pattern' => '/^[a-zA-Z0-9_\-]*$/']
        );
        
        if (!$validator) {
            $message = __('Product Feed Filename is not valid.');
            throw new LocalizedException($message);
        }
        
        return $this;
    }
}
