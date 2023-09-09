<?php

namespace MageBig\WidgetPlus\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

class ImageChooser extends Template
{
    /**
     * @var Factory
     */
    protected $_elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML.
     *
     * @param AbstractElement $element Form Element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl(
            'cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']
        );
        $chooser = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setType('button')
            ->setClass('btn-chooser')
            ->setLabel($config['button']['open'])
            ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\')');
        //->setDisabled($element->getReadonly());
        $input = $this->_elementFactory->create('text', ['data' => $element->getData()]);
        $input->setId($element->getId());
        //$input->setReadonly('readonly');
        $input->setForm($element->getForm());
        $input->setClass('widget-option bg-input input-text admin__control-text');
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }
        $script = '<script>require(["mage/adminhtml/browser"], function () {})</script>';
        $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml() . $script);

        return $element;
    }
}
