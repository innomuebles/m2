<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model;

/**
 * Class SortingAdapter
 * adapter of @see \Magento\Eav\Model\Entity\Attribute
 * used for add sorting method
 */
class SortingAdapter extends \Magento\Framework\DataObject
{
    public const CACHE_TAG = 'SORTING_METHOD';

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Sorting\Api\MethodInterface
     */
    private $methodModel;

    public function __construct(
        \Amasty\Sorting\Helper\Data $helper,
        \Amasty\Sorting\Api\MethodInterface $methodModel = null,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->methodModel = $methodModel;
        parent::__construct($data);
        $this->prepareData();
    }

    /**
     * Set Data for call object as array
     */
    private function prepareData()
    {
        if (!$this->hasData('attribute_code')) {
            $this->setData('attribute_code', $this->methodModel->getMethodCode());
        }
        if (!$this->hasData('frontend_label')) {
            $this->setData('frontend_label', $this->methodModel->getMethodName());
        }
    }

    public function getAttributeCode()
    {
        if ($this->hasData('attribute_code')) {
            return $this->_getData('attribute_code');
        }
        return $this->methodModel->getMethodCode();
    }

    public function getFrontendLabel()
    {
        if ($this->hasData('frontend_label')) {
            return $this->getData('frontend_label');
        }

        return $this->methodModel->getMethodName();
    }

    public function getDefaultFrontendLabel()
    {
        return $this->getFrontendLabel();
    }

    /**
     * Return frontend label for default store
     *
     * @return string|null
     */
    public function getStoreLabel($storeId = null)
    {
        if ($this->hasData('store_label')) {
            return $this->getData('store_label');
        }

        return $this->methodModel->getMethodLabel($storeId);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getAttributeCode()];
    }

    /**
     * @param \Amasty\Sorting\Api\MethodInterface $methodModel
     *
     * @return $this
     */
    public function setMethodModel($methodModel)
    {
        $this->methodModel = $methodModel;
        return $this;
    }

    /**
     * @return \Amasty\Sorting\Api\MethodInterface
     */
    public function getMethodModel()
    {
        return $this->methodModel;
    }

    /**
     * Frontend HTML for input element.
     *
     * @return string
     */
    public function getFrontendInput()
    {
        return 'hidden';
    }

    /**
     * Get attribute name
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return $this->getAttributeCode();
    }
    
    /**
     * @return bool
     */
    public function usesSource()
    {
        return false;
    }
}
