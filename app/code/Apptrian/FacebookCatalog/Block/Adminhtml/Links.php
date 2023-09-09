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

class Links extends \Magento\Config\Block\System\Config\Form\Field
{
    
    /**
     * @var \Apptrian\FacebookCatalog\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Apptrian\FacebookCatalog\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Apptrian\FacebookCatalog\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    
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
        
        $data = $this->helper->getProductFeedLinks();
        
        // The style is a fix for Magento 2.3.1 that makes font-size 0
        $html = '<style>.accordion .config .value.with-tooltip {font-size: 1em;}</style><div>';
        
        foreach ($data as $d) {
            $html .= '<p><span>' . $d['name'] . ':</span><br />';
            $html .= '<a href="'. $d['url'] . '" download="' . $d['filename']
                . '" target="_blank">' . $d['url'] . '</a></p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
