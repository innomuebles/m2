<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Inventory\Msi;

use Amasty\Sorting\Model\Inventory\GetQtyInterface;
use Amasty\Sorting\Model\ResourceModel\Inventory;
use Amasty\Sorting\Model\ResourceModel\Inventory\GetConfigurableQty as GetConfigurableQtyResource;
use Amasty\Sorting\Model\ResourceModel\Inventory\GetReservationQty as GetReservationQtyResource;

class GetConfigurableQty implements GetQtyInterface
{
    /**
     * @var GetConfigurableQtyResource
     */
    private $getConfigurableQtyResource;

    /**
     * @var GetReservationQtyResource
     */
    private $getReservationQtyResource;

    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(
        GetConfigurableQtyResource $getConfigurableQtyResource,
        GetReservationQtyResource $getReservationQtyResource,
        Inventory $inventory
    ) {
        $this->getConfigurableQtyResource = $getConfigurableQtyResource;
        $this->getReservationQtyResource = $getReservationQtyResource;
        $this->inventory = $inventory;
    }

    /**
     * Qty with reservation qty.
     *
     * @param string $sku
     * @param string $websiteCode
     * @return null|float
     *
     * @see \Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity::execute
     */
    public function execute(string $sku, string $websiteCode): ?float
    {
        $qty = $this->getConfigurableQtyResource->execute($sku, $websiteCode);
        if ($reservationQty = $this->getReservationQtyResource->execute(
            $sku,
            $this->inventory->getStockId($websiteCode)
        )) {
            $qty += $reservationQty;
        }

        return $qty;
    }
}
