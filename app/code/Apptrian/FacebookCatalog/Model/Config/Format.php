<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\FacebookCatalog\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class Format implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'csv', 'label' => __('CSV')],
            ['value' => 'tsv', 'label' => __('TSV')],
            ['value' => 'xml-rss', 'label' => __('XML RSS')]
        ];
    }
}
