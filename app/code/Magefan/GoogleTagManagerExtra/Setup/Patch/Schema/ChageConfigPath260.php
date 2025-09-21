<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerExtra\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ChageConfigPath260 implements SchemaPatchInterface
{

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * Constructor
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $connection = $this->schemaSetup->getConnection();

        $table = $this->schemaSetup->getTable('core_config_data');

        $changedConfigurationFields = [
            'mfgoogletagmanager/server_side_tracking/headless_storefront' => 'mfgoogletagmanager/server_container/headless_storefront',
            'mfgoogletagmanager/server_side_tracking/track_missing_purchase_events_only' => 'mfgoogletagmanager/server_container/track_missing_purchase_events_only',
        ];

        foreach ($changedConfigurationFields as $oldPath => $newPath) {
            $connection->update(
                $table,
                ['path' => $newPath],
                ['path = ?' => $oldPath]
            );
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
