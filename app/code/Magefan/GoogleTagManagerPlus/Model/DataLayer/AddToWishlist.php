<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\AddToWishlistInterface;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\Wishlist\ItemInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\Item;

class AddToWishlist extends AbstractDataLayer implements AddToWishlistInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * AddToWishlist constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ItemInterface $gtmItem
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ItemInterface $gtmItem
    ) {
        $this->gtmItem = $gtmItem;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function get(Item $wishlistItem): array
    {
        $item = $this->gtmItem->get($wishlistItem);
        $data = [
            'event' => 'add_to_wishlist',
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->getProductValue($wishlistItem->getProduct()),
                'items' => [
                    $item
                ]
            ]
        ];

        if ($wishlistItem->getWishlist() && $wishlistItem->getWishlist()->getCustomerId()) {
            $data['customer_id'] = $wishlistItem->getWishlist()->getCustomerId();
        }

        return $this->eventWrap($data);
    }
}
