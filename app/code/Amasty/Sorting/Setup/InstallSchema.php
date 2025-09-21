<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */
namespace Amasty\Sorting\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    use TableInitTrate;

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $bestsellersTable = $setup->getTable('amasty_sorting_bestsellers');
        $mostViewedTable = $setup->getTable('amasty_sorting_most_viewed');
        $wishedTable = $setup->getTable('amasty_sorting_wished');

        /**
         * Create table 'amasty_sorting_bestsellers'
         */
        $this->createBestsellers($setup, $bestsellersTable);

        /**
         * Create table 'amasty_sorting_most_viewed'
         */
        $this->createMostViewed($setup, $mostViewedTable);

        /**
         * Create table 'amasty_sorting_wished'
         */
        $this->createWished($setup, $wishedTable);

        $setup->endSetup();
    }
}
