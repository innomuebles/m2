<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */

namespace Amasty\Sorting\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/*
 * This Trait is called from UpgradeSchema is module has installed and moduleVersion < 1.2
 * Or from InstallSchema
 */
trait TableInitTrate
{
    /**
     * Create table 'amasty_sorting_bestsellers'
     *
     * @param SchemaSetupInterface $installer
     * @param $bestsellersTable
     */
    private function createBestsellers(SchemaSetupInterface $installer, $bestsellersTable)
    {
        $table = $installer->getConnection()
            ->newTable($bestsellersTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'qty_ordered',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Bestsellers'
            )->addIndex(
                $installer->getIdxName(
                    'amasty_sorting_bestsellers',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Amasty Sorting Bestsellers');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'amasty_sorting_most_viewed'
     *
     * @param SchemaSetupInterface $installer
     * @param $mostViewedTable
     */
    private function createMostViewed(SchemaSetupInterface $installer, $mostViewedTable)
    {
        $table = $installer->getConnection()
            ->newTable($mostViewedTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'views_num',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Most Viewed'
            )->addIndex(
                $installer->getIdxName(
                    'amasty_sorting_most_viewed',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Amasty Sorting Most Viewed');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'amasty_sorting_wished'
     *
     * @param SchemaSetupInterface $installer
     * @param $wishedTable
     */
    private function createWished(SchemaSetupInterface $installer, $wishedTable)
    {
        $table = $installer->getConnection()
            ->newTable($wishedTable)
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )->addColumn(
                'wished',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Wished'
            )->addIndex(
                $installer->getIdxName(
                    'amasty_sorting_wished',
                    ['product_id', 'store_id']
                ),
                ['product_id', 'store_id']
            )->setComment('Amasty Sorting Wished');

        $installer->getConnection()->createTable($table);
    }
}
