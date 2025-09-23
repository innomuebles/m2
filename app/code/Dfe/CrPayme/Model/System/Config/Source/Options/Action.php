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
 * Class Action
 */
class Action implements ArrayInterface
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
                'label' => 'Etiqueta'
            ],
            [
                'value' => '1',
                'label' => 'Circulo'
            ],
            [
                'value' => '2',
                'label' => 'Rectangulo'
            ]
        ];
    }
}
