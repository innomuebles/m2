<?php
/**
 * Payme
 *
 * Action Source Model
 *
 * @category    Payme
 * @package     Payme
 * @copyright   2019
 *
 */

namespace Dfe\CrPayme\Model\System\Config\Source\Options;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Redirect
 */
class UrlCallback extends Field
{
    

    /**
     * Get redirect url
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function getWebhookUrl()
    {

        return $this->_storeManager->getStore()->getBaseUrl('web') . 'squareupomni/hooks/notify/';
    }
}
