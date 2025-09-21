<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Setup\Patch\Data;

use Magento\Catalog\Model\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateDefaultSearch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies(): array
    {
        return [
            \Amasty\Sorting\Setup\Patch\Data\RenameLabelsField::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): UpdateDefaultSearch
    {
        if ($this->isCanProceed()) {
            $select = $this->prepareSelect(Config::XML_PATH_LIST_DEFAULT_SORT_BY);
            $tableName = $this->moduleDataSetup->getTable('core_config_data');
            $connection = $this->moduleDataSetup->getConnection();
            $rows = $connection->fetchAll($select);

            foreach ($rows as $row) {
                $updateData[] = [
                    'value' => $row['value'],
                    'path'  => 'amsorting/default_sorting/category_1',
                    'scope' => $row['scope'],
                    'scope_id' => $row['scope_id']
                ];
            }

            if (!empty($updateData)) {
                $connection->insertOnDuplicate($tableName, $updateData);
            }
        }

        return $this;
    }

    private function prepareSelect(string $path): Select
    {
        $tableName = $this->moduleDataSetup->getTable('core_config_data');
        $connection = $this->moduleDataSetup->getConnection();

        return $connection
            ->select()
            ->from($tableName, ['path', 'value', 'scope', 'scope_id'])
            ->where('path = ?', $path);
    }

    private function isCanProceed(): bool
    {
        $select = $this->prepareSelect('amsorting/default_sorting/category_1');
        $rows = $this->moduleDataSetup->getConnection()->fetchAll($select);

        return empty($rows);
    }
}
