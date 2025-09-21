<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Amasty\Sorting\Helper\Data as SortingHelper;
use Magento\Catalog\Model\Config;

class Position extends Field
{
    /**
     * @var SortingHelper
     */
    private $helper;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        SortingHelper $helper,
        Config $config,
        Context $context
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->config = $config;
    }

    protected function _construct()
    {
        $this->setTemplate('Amasty_Sorting::/position.phtml');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        $positions =  (array) $this->helper->getSortOrder();
        if ($positions === []) {
            $positions = $this->getOptionalArray();
        } else {
            $availableOptions = $this->getOptionalArray();
            // delete disabled options
            $positions = array_intersect($availableOptions, $positions);
            $newOptions = array_diff($availableOptions, $positions);
            $positions = array_merge($positions, $newOptions);
        }

        return $positions;
    }

    /**
     * @param $index
     * @return string
     */
    public function getNamePrefix($index)
    {
        return $this->getElement()->getName() . '[' . $index . ']';
    }

    private function getOptionalArray()
    {
        $positions = [];
        $methods = $this->config->getAttributeUsedForSortByArray();
        foreach ($methods as $key => $methodObject) {
            if (is_object($methodObject)) {
                $positions[$key] = $methodObject->getText();
            } else {
                $positions[$key] = $methodObject;
            }
        }

        return $positions;
    }
}
