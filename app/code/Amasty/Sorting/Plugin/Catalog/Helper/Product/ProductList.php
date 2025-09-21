<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Catalog\Helper\Product;

use Amasty\Sorting\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Helper\Product\ProductList as Subject;

class ProductList
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $searchModules = [
        'catalogsearch'
    ];

    public function __construct(
        Data $helper,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * @param Subject $subject
     * @param $sortBy
     * @return string
     */
    public function afterGetDefaultSortField(Subject $subject, $sortBy)
    {
        if (in_array($this->request->getModuleName(), $this->searchModules)) {
            $searchSortings = $this->helper->getSearchSorting();
            // getting first default sorting
            $sortBy = array_shift($searchSortings);
        }

        return $sortBy;
    }
}
