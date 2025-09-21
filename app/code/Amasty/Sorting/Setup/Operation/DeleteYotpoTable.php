<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


declare(strict_types=1);

namespace Amasty\Sorting\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class DeleteYotpoTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $oldTable = 'amasty_sorting_yotpo';
        if ($setup->tableExists($oldTable)) {
            $setup->getConnection()->dropTable($setup->getTable($oldTable));
        }
    }
}
