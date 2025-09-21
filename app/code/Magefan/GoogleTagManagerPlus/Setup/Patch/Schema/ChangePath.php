<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\GoogleTagManagerPlus\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ChangePath implements SchemaPatchInterface
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
            'mfgoogletagmanager/ads/enable' => 'mfgoogletagmanager/ads/conversion/enable',
            'mfgoogletagmanager/ads/purchase/conversion_id' => 'mfgoogletagmanager/ads/conversion/purchase/conversion_id',
            'mfgoogletagmanager/ads/purchase/conversion_label' => 'mfgoogletagmanager/ads/conversion/purchase/conversion_label',
            'mfgoogletagmanager/remarketing/enable' => 'mfgoogletagmanager/ads/remarketing/enable',
            'mfgoogletagmanager/remarketing/conversion_id' => 'mfgoogletagmanager/ads/remarketing/conversion_id',
            'mfgoogletagmanager/remarketing/conversion_label' => 'mfgoogletagmanager/ads/remarketing/conversion_label'
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
