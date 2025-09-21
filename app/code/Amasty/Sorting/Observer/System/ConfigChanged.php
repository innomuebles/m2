<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Observer\System;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Config as CatalogConfig;

class ConfigChanged implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Amasty\Sorting\Helper\Data $helper,
        CatalogConfig $catalogConfig
    ) {
        $this->reinitableConfig = $reinitableConfig;
        $this->configWriter = $configWriter;
        $this->helper = $helper;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getEvent()->getName();
        $store = (int)$observer->getEvent()->getStore() ?: null;
        $defaultSortings = $this->helper->getCategorySorting($store);
        $moduleValue = array_shift($defaultSortings);
        $catalogValue = $this->catalogConfig->getProductListDefaultSortBy($store);
        if ($catalogValue != $moduleValue) {
            switch ($name) {
                case 'admin_system_config_changed_section_catalog':
                    $this->saveAmastyValue($catalogValue, $store);
                    break;
                case 'admin_system_config_changed_section_amsorting':
                    $this->saveCatalogValue($moduleValue, $store);
                    break;
            }
        }
    }

    /**
     * @param $value
     * @param $store
     */
    private function saveAmastyValue($value, $store)
    {
        $this->saveConfig('amsorting/default_sorting/category_1', $value, $store);
    }

    /**
     * @param $value
     * @param $store
     */
    private function saveCatalogValue($value, $store)
    {
        $this->saveConfig(CatalogConfig::XML_PATH_LIST_DEFAULT_SORT_BY, $value, $store);
    }

    /**
     * @param string $path
     * @param string $value
     * @param int $store
     *
     * @return $this
     */
    private function saveConfig($path, $value, $store)
    {
        if ($store) {
            $this->configWriter->save($path, $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store);
        } else {
            $this->configWriter->save($path, $value);
        }
        $this->reinitableConfig->reinit();

        return $this;
    }
}
