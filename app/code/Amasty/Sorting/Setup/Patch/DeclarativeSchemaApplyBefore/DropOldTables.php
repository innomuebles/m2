<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Improved Sorting for Magento 2
 */

namespace Amasty\Sorting\Setup\Patch\DeclarativeSchemaApplyBefore;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class DropOldTables implements SchemaPatchInterface
{
    public const TABLES = [
        'amasty_sorting_yotpo',
        'amsorting_bestsellers',
        'amsorting_most_viewed',
        'amsorting_wished',
        'amasty_sorting_bestsellers',
        'amasty_sorting_most_viewed',
        'amasty_sorting_wished'
    ];

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    public function apply(): DropOldTables
    {
        $connection = $this->schemaSetup->getConnection();

        foreach (self::TABLES as $table) {
            $tableName = $this->schemaSetup->getTable($table);

            if ($connection->isTableExists($tableName)) {
                $connection->dropTable($tableName);
            }
        }

        return $this;
    }
}
