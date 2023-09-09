<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\FacebookCatalog\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Text extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $element = null;
        
        $text = __('This field will be populated automatically.');
        
        $html = '<div style="padding: 0 0 10px 0;">' . $text . '</div>';
        
        return $html;
    }
}
