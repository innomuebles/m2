<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Model\DataLayer;

use Magefan\GoogleTagManager\Model\AbstractDataLayer;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Quote\Model\Quote\Item;
use Magefan\GoogleTagManager\Api\DataLayer\Cart\ItemInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractActionCart extends AbstractDataLayer
{
    /**
     * @var ItemInterface
     */
    private $gtmItem;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ItemInterface $item
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        ItemInterface $item
    ) {
        $this->gtmItem = $item;
        parent::__construct($config, $storeManager, $categoryRepository);
    }

    /**
     * @inheritDoc
     */
    public function get(Item $quoteItem): array
    {
        $quote = $quoteItem->getQuote();
        $item = $this->gtmItem->get($quoteItem);
        $data = [
            'event' => $this->getEventName(),
            'ecommerce' => [
                'currency' => $this->getCurrentCurrencyCode(),
                'value' => $this->formatPrice($item['price'] * $item['quantity']),
                'items' => [$item]
            ]
        ];

        if ($quote && $quote->getCustomerId()) {
            $data['customer_id'] = $quote->getCustomerId();
        }

        return $this->eventWrap($data);
    }

    /**
     * @return string
     */
    abstract protected function getEventName(): string;
}
