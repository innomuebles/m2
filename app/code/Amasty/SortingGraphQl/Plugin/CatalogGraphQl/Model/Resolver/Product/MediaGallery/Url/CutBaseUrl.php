<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Improved Sorting GraphQl for Magento 2 (System)
 */

namespace Amasty\SortingGraphQl\Plugin\CatalogGraphQl\Model\Resolver\Product\MediaGallery\Url;

use Amasty\SortingGraphQl\Model\Resolver\Product\Image as ProductImageResolver;
use Magento\CatalogGraphQl\Model\Resolver\Product\MediaGallery\Url;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class CutBaseUrl
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param Url $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param null $value
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterResolve(
        Url $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        $value = null
    ) {
        $isAmastyQuery = ($value[ProductImageResolver::IS_AMASTY_FLAG] ?? false);
        if ($isAmastyQuery) {
            $result = str_replace(
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
                '',
                $result
            );
        }

        return $result;
    }
}
