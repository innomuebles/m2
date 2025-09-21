<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddRevenueToDescSorting implements DataPatchInterface
{
    private const DEFAULT_DESC_SORTING_PATH = 'amsorting/general/desc_attributes';
    private const REVENUE_SORTING_CODE = 'revenue';

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    public function apply(): self
    {
        $connection = $this->setup->getConnection();
        $tableName = $this->setup->getTable('core_config_data');

        $select = $connection->select()
            ->from($tableName, ['config_id', 'value'])
            ->where('path = ?', self::DEFAULT_DESC_SORTING_PATH);

        $settings = $connection->fetchAll($select);

        foreach ($settings as $row) {
            $value = $row['value'] ? $row['value'] . ',' . self::REVENUE_SORTING_CODE : self::REVENUE_SORTING_CODE;
            $connection->update(
                $tableName,
                ['value' => $value],
                ['config_id = ?' => $row['config_id']]
            );
        }

        return $this;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
