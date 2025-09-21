<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Source\Category;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * @api
 * @since 1.15.2
 */
class OptionsBuilder
{
    public const CACHE_TAG = 'AMASTY_BASE_CATEGORY_OPTIONS';

    /**
     * @var array
     */
    protected $appliedFilters = [];

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * @var Collection|null
     */
    private $collection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var string
     */
    private $itemClass;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var int
     */
    private $cacheTTL;

    public function __construct(
        CollectionFactory $collectionFactory,
        CacheInterface $cache,
        Json $serializer,
        string $itemClass = DataModel::class,
        int $cacheLiveTime = 86400 // one day
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->itemClass = $itemClass;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->cacheTTL = $cacheLiveTime;
    }

    /**
     * @param string $filterName
     * @param array|string|int|float|bool $filterValue
     */
    public function addFilter(string $filterName, $filterValue): void
    {
        $this->appliedFilters[$filterName] = $filterValue;
    }

    public function getCacheTags(): array
    {
        return [self::CACHE_TAG, CategoryModel::CACHE_TAG];
    }

    public function clear(): void
    {
        $this->collection = null;
        $this->options = [];
        $this->appliedFilters = [];
    }

    /**
     * @return string[] key-value options
     */
    public function build(): array
    {
        $isCacheEnabled = $this->cacheTTL > 0;
        if ($isCacheEnabled) {
            $cacheKey = $this->getCacheKey();
            $result = $this->cache->load($cacheKey);
            if ($result) {
                return $this->serializer->unserialize($result);
            }
        }

        foreach ($this->appliedFilters as $filterName => $filterValue) {
            $this->processFilter($filterName, $filterValue);
        }
        $categoryTree = $this->getCategoryTree();

        $this->buildOptionsByTree($categoryTree);
        $result = $this->options;
        if ($isCacheEnabled) {
            $this->cache->save(
                $this->serializer->serialize($result),
                $cacheKey,
                $this->getCacheTags(),
                $this->cacheTTL
            );
        }

        $this->clear();

        return $result;
    }

    protected function getCacheKeyParts(): array
    {
        return [self::CACHE_TAG, $this->appliedFilters];
    }

    protected function getCacheKey(): string
    {
        return sha1($this->serializer->serialize($this->getCacheKeyParts()));
    }

    /**
     * @param string $filterName
     * @param array|string|int|float|bool $filterValue
     */
    protected function processFilter(string $filterName, $filterValue): void
    {
        $this->getCollection()->addAttributeToFilter($filterName, $filterValue);
    }

    protected function getCollection(): Collection
    {
        if ($this->collection === null) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addAttributeToSelect('name');
            $this->collection->setOrder('path', 'asc');
            if ($this->itemClass) {
                // getData can't be used to load category collection data, because it doesn't load attributes
                $this->collection->setItemObjectClass($this->itemClass);
            }
        }

        return $this->collection;
    }

    /**
     * Start building nested category tree
     *
     * Find a dynamically minimal level for the case when the lowest level is not 1
     *
     * @return array
     */
    protected function getCategoryTree(): array
    {
        /** @var DataModel[] $items */
        $items = $this->getCollection()->getItems();
        $minimalLevel = (int)min(...$this->getCollection()->getColumnValues('level'));
        $tree = [];
        foreach ($items as $category) {
            if ($category->getLevel() === $minimalLevel) {
                $id = $category->getId();
                unset($items[$id]);
                $tree[$id] = [
                    'item' => $category
                ];
                if ($category->getChildrenCount()) {
                    $tree[$id]['children'] = $this->buildCategoryTree($items, $id);
                }
            }
        }
        // sort first level by position
        usort($tree, [$this, 'sortTree']);

        return $tree;
    }

    /**
     * Recursively build a category tree with nested hierarchy
     *
     * @param DataModel[] $notPrecessedItems
     * @param int $parentId
     * @return array<int, array{item: DataModel, children?: array}>
     */
    protected function buildCategoryTree(array $notPrecessedItems, int $parentId): array
    {
        $tree = [];
        foreach ($notPrecessedItems as $category) {
            if ($category->getParentId() === $parentId) {
                $id = $category->getId();
                unset($notPrecessedItems[$id]);
                $tree[$id] = [
                    'item' => $category
                ];
                if ($category->getChildrenCount()) {
                    $tree[$id]['children'] = $this->buildCategoryTree($notPrecessedItems, $id);
                }
            }
        }
        // sort current level by position
        usort($tree, [$this, 'sortTree']);

        return $tree;
    }

    /**
     * Builds single dimension array from a category nested tree
     *
     * @param array<int, array{item: DataModel, children?: array}> $tree
     * @return void
     */
    protected function buildOptionsByTree(array $tree)
    {
        foreach ($tree as $node) {
            $category = $node['item'];
            $this->options[$category->getId()] = $this->resolveOptionLabel($category);
            if (!empty($node['children'])) {
                $this->buildOptionsByTree($node['children']);
            }
        }
    }

    protected function resolveOptionLabel(DataModel $category): string
    {
        return str_repeat('. ', max(0, ($category->getLevel() - 1) * 3)) . $category->getName();
    }

    protected function sortTree(array $catA, array $catB): int
    {
        return $catA['item']->getPosition() <=> $catB['item']->getPosition();
    }
}
