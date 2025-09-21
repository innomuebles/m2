<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */

namespace Amasty\Sorting\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    use TableInitTrate;

    /**
     * @var Operation\RenameLabelsField
     */
    private $renameLabelsField;

    /**
     * @var Operation\DeleteYotpoTable
     */
    private $deleteYotpoTable;

    public function __construct(
        Operation\RenameLabelsField $renameLabelsField,
        Operation\DeleteYotpoTable $deleteYotpoTable
    ) {
        $this->renameLabelsField = $renameLabelsField;
        $this->deleteYotpoTable = $deleteYotpoTable;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $bestsellersTable = $setup->getTable('amasty_sorting_bestsellers');
        $mostViewedTable = $setup->getTable('amasty_sorting_most_viewed');
        $wishedTable = $setup->getTable('amasty_sorting_wished');

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.0', '<')) {
            if (!$setup->getConnection()->isTableExists($bestsellersTable)) {
                /**
                 * Create table 'amasty_sorting_bestsellers'
                 */
                $this->createBestsellers($setup, $bestsellersTable);
                if ($setup->getConnection()->isTableExists($setup->getTable('amsorting_bestsellers'))) {
                    $setup->getConnection()->dropTable('amsorting_bestsellers');
                }
            }

            if (!$setup->getConnection()->isTableExists($mostViewedTable)) {
                /**
                 * Create table 'amasty_sorting_most_viewed'
                 */
                $this->createMostViewed($setup, $mostViewedTable);
                if ($setup->getConnection()->isTableExists($setup->getTable('amsorting_most_viewed'))) {
                    $setup->getConnection()->dropTable('amsorting_most_viewed');
                }
            }

            if (!$setup->getConnection()->isTableExists($wishedTable)) {
                /**
                 * Create table 'amasty_sorting_wished'
                 */
                $this->createWished($setup, $wishedTable);
                if ($setup->getConnection()->isTableExists($setup->getTable('amsorting_wished'))) {
                    $setup->getConnection()->dropTable('amsorting_wished');
                }
            }
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->addPrimaryKeys($setup, [$bestsellersTable, $mostViewedTable, $wishedTable]);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.5.2', '<')) {
            $this->renameLabelsField->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.8.2', '<')) {
            $this->deleteYotpoTable->execute($setup);
        }

        $setup->endSetup();
    }

    /**
     * Set columns product_id, store_id as primary keys
     *
     * @param SchemaSetupInterface $setup
     * @param array $tables
     */
    private function addPrimaryKeys(SchemaSetupInterface $setup, array $tables)
    {
        foreach ($tables as $table) {
            if ($setup->getConnection()->isTableExists($table)) {
                $setup->getConnection()->changeColumn(
                    $table,
                    'product_id',
                    'product_id',
                    ['type' => Table::TYPE_INTEGER, 'primary' => true],
                    'Product ID'
                );
                $setup->getConnection()->changeColumn(
                    $table,
                    'store_id',
                    'store_id',
                    ['type' => Table::TYPE_SMALLINT, 'primary' => true],
                    'Store Id'
                );
            }
        }
    }
}
