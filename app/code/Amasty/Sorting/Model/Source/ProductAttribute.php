<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Source;

/**
 * Class ProductAttribute
 */
class ProductAttribute implements \Magento\Framework\Data\OptionSourceInterface
{
    private $options;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * ProductAttribute constructor.
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [['value' => '', 'label' => ' ']];
            $attributes = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getAttributeCollection()
                ->addFieldToFilter('frontend_input', ['nin' => ['multiselect', 'gallery', 'textarea']])
                ->addFieldToFilter('used_in_product_listing', 1)
                ->getItems();

            foreach ($attributes as $item) {
                $this->options[] = [
                    'value' => $item->getAttributeCode(),
                    'label' => __($item->getFrontendLabel()),
                ];
            }
        }

        return $this->options;
    }
}
