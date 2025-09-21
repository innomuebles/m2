<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\Catalog\Model\Category\Attribute\Source;

use Magento\Catalog\Model\Config as CatalogConfig;

class Sortby
{
    /**
     * @var CatalogConfig
     */
    protected $_catalogConfig;

    /**
     * @var array
     */
    protected $_options = null;

    /**
     * @param CatalogConfig $catalogConfig
     */
    public function __construct(CatalogConfig $catalogConfig)
    {
        $this->_catalogConfig = $catalogConfig;
    }

    /**
     * @return CatalogConfig
     */
    protected function _getCatalogConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundGetAllOptions($subject, $proceed)
    {
        if ($this->_options === null) {
            foreach ($this->_getCatalogConfig()->getAttributeUsedForSortByArray() as $code => $attribute) {
                $this->_options[] = [
                    'label' => __(is_array($attribute) ? $attribute->getText() : $attribute),
                    'value' => $code
                ];
            }
        }
        return $this->_options;
    }
}
