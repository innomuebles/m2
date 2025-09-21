<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Model\Events;

use Magefan\GoogleTagManagerExtra\Api\Events\ViewItemInterface;
use Magefan\GoogleTagManagerExtra\Api\ServerTrackerInterface as ServerTracker;
use Magefan\GoogleTagManager\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magefan\GoogleTagManager\Model\DataLayer\ViewItem as ViewItemDataLayer;

/**
 * Abstract management model
 */
class ViewItem implements ViewItemInterface
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
     * @var ViewItemDataLayer
     */
    protected $viewItem;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ServerTracker $serverTracker
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     * @param ViewItemDataLayer $viewItem
     */
    public function __construct(
        ServerTracker $serverTracker,
        Config $config,
        ProductRepositoryInterface $productRepository,
        ViewItemDataLayer $viewItem
    ) {
        $this->serverTracker = $serverTracker;
        $this->config = $config;
        $this->productRepository = $productRepository;
        $this->viewItem = $viewItem;
    }

    /**
     * @param string $itemId
     * @return mixed
     */
    public function execute(string $productId)
    {
        if ($this->config->isEnabled() && $this->serverTracker->isEnabled()) {
            $product = $this->productRepository->getById($productId);
            $data = $this->viewItem->get($product);
            $this->serverTracker->push($data);
        }
    }
}
