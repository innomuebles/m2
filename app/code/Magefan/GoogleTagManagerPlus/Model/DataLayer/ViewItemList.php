<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer;

use Magefan\GoogleTagManager\Api\DataLayer\Product\ItemInterface;
use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magefan\GoogleTagManagerPlus\Api\DataLayer\ViewItemListInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class ViewItemList extends AbstractDataLayer implements ViewItemListInterface
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * @var string
     */
    public $itemListName;

    /**
     * ViewItemList constructor.
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
     */
    public function get(array $productItems): array
    {
        $items = [];

        foreach ($productItems as $item) {
            $items[] = $this->gtmItem->get($item);
        }

        return $this->eventWrap([
            'event' => 'view_item_list',
            'ecommerce' => [
                'item_list_id' => str_replace(' ', '_', strtolower($this->getItemListName())),
                'item_list_name' => $this->getItemListName(),
                'items' => $items
            ]
        ]);
    }

    /**
     * Set item list name
     *
     * @param string $name
     */
    public function setItemListName(string $name)
    {
        $this->itemListName = $name;
    }

    /**
     * Get item list name
     *
     * @return string
     */
    public function getItemListName(): string
    {
        return $this->itemListName;
    }
}
