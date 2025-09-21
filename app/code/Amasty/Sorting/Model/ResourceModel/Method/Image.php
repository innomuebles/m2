<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\Elasticsearch\IsElasticSort;
use Amasty\Sorting\Model\IsSearchPage;
use Amasty\Sorting\Model\Source\Image as ImageSource;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Escaper;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as ProductResource;

/**
 * Class Image
 * Method Using like additional sorting and not visible in the list of methods
 */
class Image extends AbstractMethod
{
    /**
     * @var IsSearchPage
     */
    private $isSearchPage;

    public function __construct(
        Context $context,
        Escaper $escaper,
        ConfigProvider $configProvider,
        IsElasticSort $isElasticSort,
        $connectionName = null,
        $methodCode = '',
        $methodName = ''
    ) {
        parent::__construct(
            $context,
            $escaper,
            $configProvider,
            $isElasticSort,
            $connectionName,
            $methodCode,
            $methodName
        );
        $this->isSearchPage = $context->getIsSearchPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortingColumnName()
    {
        return 'small_image';
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $direction = '')
    {
        if (!$this->isMethodActive($collection) || $this->isMethodAlreadyApplied($collection)) {
            return $this;
        }

        $attribute = $this->getSortingColumnName();

        $collection->addAttributeToSelect($attribute, 'left');
        $collection->getSelect()->order($this->getSortExpression($attribute));

        $orders = $collection->getSelect()->getPart(Select::ORDER);
        // move from the last to the the first position
        array_unshift($orders, array_pop($orders));
        $collection->getSelect()->setPart(Select::ORDER, $orders);

        $this->markApplied($collection);

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isMethodActive(ProductCollection $collection): bool
    {
        $show = $this->helper->getNonImageLast();

        if (!$show || ($show == ImageSource::SHOW_LAST_FOR_CATALOG && $this->isSearchPage->execute())) {
            return false;
        }

        return true;
    }

    /**
     * If image value is no_selection then drop value to down of the list
     * return IF(IFNULL(e.small_image, 'no_selection')='no_selection', 1, 0)
     *
     * @return \Zend_Db_Expr
     */
    private function getSortExpression($imageColumn)
    {
        $connection = $this->getConnection();
        $noSelection = $connection->quote(ProductResource::NOT_SELECTED_IMAGE);
        /** IFNULL(e.small_image, 'no_selection') */
        $ifNull = $connection->getIfNullSql($imageColumn, $noSelection);
        /** IFNULL(e.small_image, 'no_selection')='no_selection' */
        $ifNull .= '=' . $noSelection;

        /** IF(IFNULL(e.small_image, 'no_selection')='no_selection', 1, 0) */
        return $connection->getCheckSql($ifNull, 1, 0);
    }

    /**
     * @inheritdoc
     */
    public function getIndexedValues(int $storeId, ?array $entityIds = [])
    {
        return [];
    }
}
