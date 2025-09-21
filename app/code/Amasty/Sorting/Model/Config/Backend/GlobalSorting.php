<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Config\Backend;

use Amasty\Sorting\Model\MethodProvider;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GlobalSorting extends Value
{
    /**
     * @return GlobalSorting
     * @throws LocalizedException
     */
    public function afterSave(): GlobalSorting
    {
        if ($this->isValueChanged()
            && $this->getValue()
            && $this->getMethodProvider()->getMethodByCode($this->getValue()) === null
            && !$this->isAttributeExists($this->getValue())
        ) {
            throw new LocalizedException(
                __('Please input valid attribute name or Amasty sorting method for global sorting.')
            );
        }

        return parent::afterSave();
    }

    /**
     * If attribute code is numeric return false, because attribute_code not support numeric values.
     *
     * @param string $attributeCode
     * @return bool
     */
    private function isAttributeExists(string $attributeCode): bool
    {
        if (is_numeric($attributeCode)) {
            return false;
        }

        try {
            $this->getAttributeRepository()->get($attributeCode);
            $result = true;
        } catch (NoSuchEntityException $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @return MethodProvider
     */
    private function getMethodProvider(): MethodProvider
    {
        return $this->getData('method_provider');
    }

    /**
     * @return ProductAttributeRepositoryInterface
     */
    private function getAttributeRepository(): ProductAttributeRepositoryInterface
    {
        return $this->getData('attribute_repository');
    }
}
