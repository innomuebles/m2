<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Barcode
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barcode\Block\Adminhtml\Product\Import;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Mageplaza\Barcode\Helper\Data as HelperData;

/**
 * Class Button
 * @package Mageplaza\Barcode\Block\Adminhtml\Product\Import
 */
class Button extends Container
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param array $data
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData,
        Context $context,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }
    /**
     * @return Container
     */
    protected function _prepareLayout()
    {
        if ($this->_helperData->isEnabled()) {
            $buttonData = [
                'id'             => 'mp_barcode_import_btn',
                'label'          => __('Print Barcode'),
                'class'          => 'add action-secondary',
                'button_class'   => 'primary',
                'data_attribute' => [
                    'mage-init' => [
                        'Magento_Ui/js/form/button-adapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'product_listing.product_listing.listing_top.listing_massaction',
                                    'actionName' => 'toggleImportBarcodeModal'
                                ],
                            ]
                        ]
                    ]
                ],
            ];
            $this->buttonList->add('mp_barcode_import_btn', $buttonData);
        }

        return parent::_prepareLayout();
    }
}
