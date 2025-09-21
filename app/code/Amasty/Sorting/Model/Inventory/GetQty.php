<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Inventory;

use Magento\Framework\Module\Manager as ModuleManager;

class GetQty implements GetQtyInterface
{
    /**
     *
     * @var array Array [
     *  'website_code' => [
     *      'sku' => 'qty'
     *      ...
     *  ]
     *  ...
     * ]
     */
    private $qty = [];

    /**
     * @var GetQtyInterface
     */
    private $getCatalogInventoryQtyByType;

    /**
     * @var GetQtyInterface
     */
    private $getMsiQtyByType;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(
        GetQtyInterface $getCatalogInventoryQtyByType,
        GetQtyInterface $getMsiQtyByType,
        ModuleManager $moduleManager
    ) {
        $this->getCatalogInventoryQtyByType = $getCatalogInventoryQtyByType;
        $this->getMsiQtyByType = $getMsiQtyByType;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param string $sku
     * @param string $websiteCode
     * @return null|float
     */
    public function execute(string $sku, string $websiteCode): ?float
    {
        if (!isset($this->qty[$websiteCode][$sku])) {
            if ($this->moduleManager->isEnabled('Magento_Inventory')) {
                $qty = $this->getMsiQtyByType->execute($sku, $websiteCode);
            } else {
                $qty = $this->getCatalogInventoryQtyByType->execute($sku, $websiteCode);
            }
            $this->qty[$websiteCode][$sku] = $qty;
        }

        return $this->qty[$websiteCode][$sku];
    }
}
