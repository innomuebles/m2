<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Model\Indexer;

use Amasty\Sorting\Api\IndexedMethodInterface;
use Amasty\Sorting\Model\ConfigProvider;
use Amasty\Sorting\Model\Method\IsMethodEnabled;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Registry;

class AbstractIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var IndexedMethodInterface
     */
    private $indexBuilder;

    /**
     * @var CacheTypeListInterface
     */
    private $cache;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var IsMethodEnabled
     */
    private $isMethodEnabled;

    public function __construct(
        IndexedMethodInterface $indexBuilder,
        CacheTypeListInterface $cache,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        Registry $registry,
        ConfigProvider $configProvider,
        IsMethodEnabled $isMethodEnabled
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->cache = $cache;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->registry = $registry;
        $this->configProvider = $configProvider;
        $this->isMethodEnabled = $isMethodEnabled;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        // do full reindex if method is not disabled
        if ($this->isMethodEnabled->execute($this->indexBuilder->getMethodCode())
            && !$this->registry->registry('reindex_' . $this->indexBuilder->getMethodCode())
        ) {
            $this->indexBuilder->reindex();
            $this->cacheContext->registerTags(
                ['sorted_by_' . $this->indexBuilder->getMethodCode()]
            );
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
            $this->registry->register('reindex_' . $this->indexBuilder->getMethodCode(), true);
        }
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function executeList(array $ids)
    {
        if (!$ids) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array')
            );
        }
        $this->doExecuteList($ids);
    }

    /**
     * TODO: implement partial reindex
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    protected function doExecuteList($ids) : void
    {
    }

    /**
     * TODO: implement partial reindex
     *
     * @param int $id
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    private function doExecuteRow($id): void
    {
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function executeRow($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        $this->doExecuteRow($id);
    }
}
