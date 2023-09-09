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

class AdditionalImageLink extends \Magento\Framework\App\Config\Value
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
            ['pattern' => '/^[0-9]*$/']
        );
        
        if (!$validator) {
            $message = __('Additional Image Link limit is not valid.');
            throw new LocalizedException($message);
        }
        
        if ($value > 10) {
            $message = __(
                'Allowed values for Additional Image Link Limit are 0 to 10.'
            );
            throw new LocalizedException($message);
        }
        
        return $this;
    }
}
