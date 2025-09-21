<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\Catalog;

use Amasty\Sorting\Model\Method\IsMethodDisplayed;
use Amasty\Sorting\Model\MethodProvider;
use Amasty\Sorting\Model\Source\SortOptions;
use Amasty\Sorting\Model\SortingAdapterFactory;
use Magento\Catalog\Model\Config as CatalogEavConfig;
use Magento\Framework\View\LayoutInterface;

class Config
{
    /**
     * @var MethodProvider
     */
    private $methodProvider;

    /**
     * @var SortingAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var SortOptions
     */
    private $sortOptions;

    /**
     * @var IsMethodDisplayed
     */
    private $isMethodDisplayed;

    public function __construct(
        MethodProvider $methodProvider,
        SortingAdapterFactory $adapterFactory,
        LayoutInterface $layout,
        SortOptions $sortOptions,
        IsMethodDisplayed $isMethodDisplayed
    ) {
        $this->methodProvider = $methodProvider;
        $this->adapterFactory = $adapterFactory;
        $this->layout = $layout;
        $this->sortOptions = $sortOptions;
        $this->isMethodDisplayed = $isMethodDisplayed;
    }

    /**
     * Retrieve Attributes array used for sort by
     *
     * @param CatalogEavConfig $subject
     * @param array $options
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAttributesUsedForSortBy(CatalogEavConfig $subject, array $options): array
    {
        foreach ($options as $key => $option) {
            if (!$this->isMethodDisplayed->execute($key)) {
                unset($options[$key]);
            }
        }

        return $this->addNewOptions($options);
    }

    /**
     * @param array $options
     * @return array
     */
    public function addNewOptions(array $options): array
    {
        $methods = $this->methodProvider->getMethods();

        foreach ($methods as $methodObject) {
            $code = $methodObject->getMethodCode();
            if ($this->isMethodDisplayed->execute($code) && !isset($options[$code])) {
                $options[$code] = $this->adapterFactory->create(['methodModel' => $methodObject]);
            }
        }

        return $options;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @param CatalogEavConfig $subject
     * @param array $options
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAttributeUsedForSortByArray(CatalogEavConfig $subject, array $options): array
    {
        if (!$this->isMethodDisplayed->execute('position')) {
            unset($options['position']);
        }

        $options = $this->sortOptions->execute($options);

        if (count($options) == 0 && !$this->layout->getBlock('search.result')) {
            $options[] = '';
        }

        return $options;
    }
}
