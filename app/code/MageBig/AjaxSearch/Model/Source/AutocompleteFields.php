<?php

namespace MageBig\AjaxSearch\Model\Source;

class AutocompleteFields
{
    const SUGGEST = 'suggest';

    const PRODUCT = 'product';

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PRODUCT, 'label' => __('Products')],
            ['value' => self::SUGGEST, 'label' => __('Suggested')]
        ];
    }
}
