<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Elasticsearch;

use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManager;

class IsElasticSort
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManager
     */
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManager $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function execute(bool $skipStoreCheck = false): bool
    {
        return $this->checkSearchEngine($this->scopeConfig->getValue(EngineInterface::CONFIG_ENGINE_PATH))
            && ($skipStoreCheck || $this->storeManager->getStore()->getId());
    }

    private function checkSearchEngine(?string $engineName): bool
    {
        return strpos($engineName, 'elast') !== false || $engineName === 'opensearch';
    }
}
