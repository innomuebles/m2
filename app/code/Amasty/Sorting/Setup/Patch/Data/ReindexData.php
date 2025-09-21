<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Setup\Patch\Data;

use Amasty\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Amasty\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Amasty\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class ReindexData implements DataPatchInterface, NonTransactionableInterface
{
    private $indexerIds = [
        BestsellersProcessor::INDEXER_ID,
        MostViewedProcessor::INDEXER_ID,
        WishedProcessor::INDEXER_ID
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        IndexerRegistry $indexerRegistry,
        State $state
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->indexerRegistry = $indexerRegistry;
        $this->appState = $state;
    }

    public static function getDependencies(): array
    {
        return [
            \Amasty\Sorting\Setup\Patch\Data\RenameLabelsField::class,
            \Amasty\Sorting\Setup\Patch\Data\UpdateDefaultSearch::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): ReindexData
    {
        foreach ($this->indexerIds as $indexerId) {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this, 'reindexData'],
                [$indexerId]
            );
        }

        return $this;
    }

    public function reindexData(string $indexerId): void
    {
        $indexer = $this->indexerRegistry->get($indexerId);
        $indexer->invalidate();
        $indexer->reindexAll();
    }
}
