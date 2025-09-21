<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\Sorting\Model\Method\GetAttributeCodesForSorting;

use Amasty\Sorting\Model\Method\GetAttributeCodesForSorting;
use Amasty\SortingGraphQl\Model\MethodProvider\CodeMap;

class DisableCodeMap
{
    /**
     * @var CodeMap
     */
    private $codeMap;

    public function __construct(CodeMap $codeMap)
    {
        $this->codeMap = $codeMap;
    }

    /**
     * @see GetAttributeCodesForSorting::execute
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(GetAttributeCodesForSorting $subject, callable $proceed)
    {
        $codeMap = $this->codeMap->getMap();

        $this->codeMap->setMap(null);
        $result = $proceed();
        $this->codeMap->setMap($codeMap);

        return $result;
    }
}
