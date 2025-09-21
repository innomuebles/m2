<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer;
use Amasty\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Amasty\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Amasty\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\App\State;

class InstallData implements InstallDataInterface
{
    /**
     * @var IndexerFactory
     */
    private $indexer;

    /**
     * @var array
     */
    private $indexerIds = [
        BestsellersProcessor::INDEXER_ID,
        MostViewedProcessor::INDEXER_ID,
        WishedProcessor::INDEXER_ID
    ];

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var Operation\UpdateDefaultSearch
     */
    private $defaultSearch;

    public function __construct(
        IndexerFactory $indexer,
        State $state,
        Operation\UpdateDefaultSearch $defaultSearch
    ) {
        $this->state = $state;
        $this->indexer = $indexer;
        $this->defaultSearch = $defaultSearch;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->emulateAreaCode(
            'adminhtml',
            [$this, 'reindexAll']
        );

        $this->defaultSearch->execute($setup);
    }

    public function reindexAll()
    {
        foreach ($this->indexerIds as $indexerId) {
            $this->loadIndexer($indexerId)->reindexAll();
        }
    }

    /**
     * @param int $indexerId
     *
     * @return Indexer
     */
    private function loadIndexer($indexerId)
    {
        return $this->indexer->create()
            ->load($indexerId);
    }
}
