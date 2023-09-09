<?php

namespace MageBig\MbLib\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\ModuleListInterface;

class ModuleInfo extends Field
{
    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param array $data
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        array $data = []
    ) {
        $this->_moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    /**
     * Return info block html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $moduleCode = $this->getModuleName();
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        $version = $moduleInfo['setup_version'];

        $html = '<div style="padding:10px;background-color:#f2f2f2;border:1px solid #ccc;margin-bottom:5px;">
            ' . $this->getModuleTitle($moduleCode) . ' v' . $version . ' is developed by ';
        if ($this->getModuleUrl()) {
            $html .= '<a href="' . $this->getModuleUrl() . '" target="_blank">MAGEBIG</a>';
        } else {
            $html .= '<strong>MAGEBIG</strong>';
        }
        $html .= '.</div>';

        return $html;
    }

    /**
     * Return extension url
     *
     * @return string
     */
    protected function getModuleUrl()
    {
        return 'https://www.magebig.com/';
    }

    /**
     * Return extension title
     *
     * @param string $code
     * @return string
     */
    protected function getModuleTitle(string $code)
    {
        return ucwords(str_replace('MageBig_', ' ', $code)) . ' Extension';
    }
}
