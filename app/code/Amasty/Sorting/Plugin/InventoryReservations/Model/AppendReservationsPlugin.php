<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Plugin\InventoryReservations\Model;

use Amasty\Base\Model\Serializer;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class AppendReservationsPlugin
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Serializer $serializer,
        OrderRepositoryInterface $orderRepository,
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\InventoryReservations\Model\AppendReservations $subject
     * @param array $reservations
     * @return array
     */
    public function beforeExecute($subject, array $reservations)
    {
        $parentReservations = [];
        if ($reservations) {
            $reservation = reset($reservations);
            try {
                $metadata = $this->unserialize($reservation->getMetadata());
                if (isset($metadata['object_type'])
                    && !empty($metadata['object_id'])
                    && $metadata['object_type'] == 'order'
                ) {
                    $order = $this->orderRepository->get($metadata['object_id']);
                    foreach ($reservations as $reservation) {
                        if ($newReservation = $this->createParentReservation($order, $reservation)) {
                            $parentReservations[] = $newReservation;
                        }
                    }
                }
            } catch (\InvalidArgumentException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return [array_merge($reservations, $parentReservations)];
    }

    /**
     * @param OrderInterface $order
     * @param ReservationInterface $reservation
     * @return bool|ReservationInterface
     */
    private function createParentReservation($order, $reservation)
    {
        $parentReservation = false;

        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->getParentItemId()
                && $orderItem->getParentItem()->getProductType() == Configurable::TYPE_CODE
                && $orderItem->getParentItem()->getProduct()
                && $orderItem->getSku() == $reservation->getSku()
            ) {
                try {
                    $parentReservation = $this->getReservationBuilder()
                        ->setSku($orderItem->getParentItem()->getProduct()->getSku())
                        ->setQuantity((float)$reservation->getQuantity())
                        ->setStockId($reservation->getStockId())
                        ->setMetadata($reservation->getMetadata())
                        ->build();
                    break;
                } catch (ValidationException $e) {
                    $this->logger->error($e->getMessage());
                    $parentReservation = false;
                    continue;
                }
            }
        }

        return $parentReservation;
    }

    /**
     * @param $data
     * @return array|bool|float|int|string|null
     * @throws \InvalidArgumentException
     */
    private function unserialize($data)
    {
        return $this->serializer->unserialize($data);
    }

    /**
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    private function getReservationBuilder()
    {
        return $this->objectManager->get(
            \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface::class
        );
    }
}
