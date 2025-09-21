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
use Amasty\Sorting\Model\ResourceModel\Inventory\GetReservationQty as GetReservationQtyResource;
use Amasty\Sorting\Model\ResourceModel\Inventory\GetSimpleQty as GetSimpleQtyResource;

class GetSimpleQty implements GetQtyInterface
{
    /**
     * @var GetSimpleQtyResource
     */
    private $getSimpleQtyResource;

    /**
     * @var GetReservationQtyResource
     */
    private $getReservationQtyResource;

    /**
     * @var Inventory
     */
    private $inventory;

    public function __construct(
        GetSimpleQtyResource $getSimpleQtyResource,
        GetReservationQtyResource $getReservationQtyResource,
        Inventory $inventory
    ) {
        $this->getSimpleQtyResource = $getSimpleQtyResource;
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
        $qty = $this->getSimpleQtyResource->execute($sku, $websiteCode);
        if ($reservationQty = $this->getReservationQtyResource->execute(
            $sku,
            $this->inventory->getStockId($websiteCode)
        )) {
            $qty += $reservationQty;
        }

        return $qty;
    }
}
