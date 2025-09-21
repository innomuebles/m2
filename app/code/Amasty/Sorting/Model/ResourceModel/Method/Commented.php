<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

class Commented extends Toprated
{
    /**
     * Returns Sorting method Table Column name
     * which is using for order collection
     *
     * @return string
     */
    public function getSortingColumnName()
    {
        return 'reviews_count';
    }

    /**
     * @return string
     */
    public function getSortingFieldName()
    {
        return $this->helper->isYotpoEnabled() ? 'total_reviews' : 'reviews_count';
    }
}
