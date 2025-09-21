<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Catalog\Product;

use Amasty\Sorting\Model\Logger as AmastyLogger;
use Amasty\Sorting\Model\MethodProvider;
use Magento\Catalog\Block\Product\ListProduct as NativeList;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class ListProduct
{
    /**
     * @var MethodProvider
     */
    private $methodProvider;

    /**
     * @var AmastyLogger
     */
    private $logger;

    public function __construct(MethodProvider $methodProvider, AmastyLogger $logger)
    {
        $this->methodProvider = $methodProvider;
        $this->logger = $logger;
    }

    /**
     * @param NativeList $subject
     * @param array $identities
     *
     * @return array
     */
    public function afterGetIdentities(NativeList $subject, $identities)
    {
        /** @var Toolbar $toolbarBlock */
        $toolbarBlock = $subject->getLayout()->getBlock('product_list_toolbar');
        if ($toolbarBlock
            && in_array(
                $toolbarBlock->getCurrentOrder(),
                array_keys($this->methodProvider->getIndexedMethods())
            )
        ) {
            $identities[] = 'sorted_by_' . $toolbarBlock->getCurrentOrder();
        }

        return $identities;
    }

    /**
     * @param NativeList $subject
     * @param AbstractCollection $result
     *
     * @return AbstractCollection
     */
    public function afterGetLoadedProductCollection(NativeList $subject, $result)
    {
        $this->logger->logCollectionQuery($result);

        return $result;
    }
}
