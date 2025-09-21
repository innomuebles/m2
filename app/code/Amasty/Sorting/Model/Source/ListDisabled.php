<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

use Magento\Catalog\Model\Config;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Registry;

class ListDisabled implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Config $catalogConfig, Registry $registry)
    {
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        $this->registry->unregister('sorting_all_attributes');
        $this->registry->register('sorting_all_attributes', true);
        $allAttributes = $this->catalogConfig->getAttributeUsedForSortByArray();
        $this->registry->unregister('sorting_all_attributes');
        foreach ($allAttributes as $code => $label) {
            $options[] = [
                'label' => $label,
                'value' => $code
            ];
        }

        return $options;
    }
}
