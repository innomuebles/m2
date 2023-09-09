<?php
/**
 * Copyright © 2020 MageBig, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageBig\Shopbybrand\Ui\Component\Listing\Column;

use MageBig\Shopbybrand\Helper\Data as ShopbybrandHelper;
use MageBig\Shopbybrand\Model\Brand;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ShopbybrandPreview extends Column
{
    protected $helper;
    protected $brandModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ShopbybrandHelper $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShopbybrandHelper $helper,
        Brand $brandModel,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->brandModel = $brandModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['option_id'])) {
                    if (isset($item['brand_object'])) {
                        $brand = $item['brand_object'];
                    } else {
                        $this->brandModel->setOptionId($item['option_id']);
                        $brand = $this->brandModel->load(null);
                        $item['brand_object'] = $brand;
                    }
                    $item[$name]['edit'] = [
                        'href' => $this->helper->getBrandPageUrl($brand),
                        'label' => __('Preview'),
                        'target' => 'blank'
                    ];
                }
            }
        }
        return $dataSource;
    }
}
