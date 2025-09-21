<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\ViewItemListInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magefan\GoogleTagManagerPlus\Model\DataLayer\ViewItemList as ViewItemListDataLayer;

/**
 * Abstract management model
 */
class ViewItemList implements ViewItemListInterface
{

    /**
     * @var ServerTracker
     */
    private $serverTracker;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ViewItemListDataLayer
     */
    protected $viewItemList;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ServerTracker $serverTracker
     * @param ProductRepositoryInterface $productRepository
     * @param ViewItemListDataLayer $viewItemList
     * @param Config $config
     */
    public function __construct(
        ServerTracker $serverTracker,
        ProductRepositoryInterface $productRepository,
        ViewItemListDataLayer $viewItemList,
        Config $config
    ) {
        $this->serverTracker = $serverTracker;
        $this->productRepository = $productRepository;
        $this->viewItemList = $viewItemList;
        $this->config = $config;
    }

    /**
     * @param string $listType
     * @param string $productIdentifiers
     * @return mixed
     */
    public function execute(string $listType, string $productIdentifiers)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $products = [];
            $productSkus = explode('_', $productIdentifiers);
            foreach ($productSkus as $productSku) {
                $product = $this->productRepository->get($productSku);
                array_push($products, $product);
            }

            if ($listType === 'catalog') {
                $this->viewItemList->setItemListName('Catalog Widget products');
            } elseif ($listType === 'related') {
                $this->viewItemList->setItemListName('Related products');
            } elseif ($listType === 'cross') {
                $this->viewItemList->setItemListName('Cross-sell products');
            } elseif ($listType === 'upsell') {
                $this->viewItemList->setItemListName('Up-sell products');
            }
            $data = $this->viewItemList->get($products);
            $this->serverTracker->push($data);
        }
    }
}
