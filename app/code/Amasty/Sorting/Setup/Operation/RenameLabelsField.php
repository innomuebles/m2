<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class RenameLabelsField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $updateData = [];
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('core_config_data');

        $select = $setup->getConnection()->select()
            ->from($tableName, ['path', 'value', 'scope', 'scope_id'])
            ->where('path = ?', 'amsorting/biggest_saving/label');

        $rows = $connection->fetchAll($select);
        foreach ($rows as $row) {
            $updateData[] = [
                'value' => $row['value'],
                'path'  => 'amsorting/saving/label',
                'scope' => $row['scope'],
                'scope_id' => $row['scope_id']
            ];
        }

        if (!empty($updateData)) {
            $connection->insertOnDuplicate($tableName, $updateData);
        }
    }
}
