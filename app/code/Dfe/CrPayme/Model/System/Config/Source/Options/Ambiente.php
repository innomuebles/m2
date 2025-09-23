<?php
/**
 * SquareUp
 *
 * Action Source Model
 *
 * @category    Payme
 * @package     Payme
 * @copyright   2019
 *
 */

namespace Dfe\CrPayme\Model\System\Config\Source\Options;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Ambiente
 */
class Ambiente implements ArrayInterface
{
    /**
     * To option array
     *
     * @return array
     */
    function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => 'Integracion'
            ],
            [
                'value' => '1',
                'label' => 'Producción'
            ]
        ];
    }
}
