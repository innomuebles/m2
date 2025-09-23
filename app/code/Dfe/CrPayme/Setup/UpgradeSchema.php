<?php
/**
 * SquareUp
 *
 * UpgradeSchema Setup
 *
 * @category    Squareup
 * @package     Squareup_Omni
 * @copyright   2018
 * @author      SquareUp
 */

namespace Squareup\Omni\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Version 0.1.1
         * Add squareup_omni_location table
         */

              $table = $setup->getConnection()
              ->newTable($setup->getTable('payme_log'))
              ->addColumn(
                  'id_log',
                  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                  null,
                  ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                  'id_log ID'
              )
              ->addColumn(
                  'id_order',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'id_order'
              )
              ->addColumn(
                  'authorizationResult',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'authorizationResult'
              )
              ->addColumn(
                  'authorizationCode',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'authorizationCode'
              )
              ->addColumn(
                  'errorCode',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'errorCode'
              )
              ->addColumn(
                  'errorMessage',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'errorMessage'
              )
              ->addColumn(
                  'bin',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'bin'
              )
              ->addColumn(
                  'brand',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'brand'
              )
              ->addColumn(
                  'paymentReferenceCode',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'paymentReferenceCode'
              )
              ->addColumn(
                  'purchaseOperationNumber',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseOperationNumber'
              )
              ->addColumn(
                  'purchaseAmount',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseAmount'
              )
              ->addColumn(
                  'purchaseCurrencyCode',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseCurrencyCode'
              )
              ->addColumn(
                  'purchaseVerification',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseVerification'
              )
              ->addColumn(
                  'plan',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'plan'
              )
              ->addColumn(
                  'cuota',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'cuota'
              )
              ->addColumn(
                  'montoAproxCuota',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'montoAproxCuota'
              )
              ->addColumn(
                  'resultadoOperacion',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'resultadoOperacion'
              )
              ->addColumn(
                  'paymethod',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'paymethod'
              )
              ->addColumn(
                  'fechaHora',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'fechaHora'
              )
              ->addColumn(
                  'reserved1',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved1'
              )
             ->addColumn(
                  'reserved2',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved2'
              )->addColumn(
                  'reserved3',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved3'
              )->addColumn(
                  'reserved4',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved4'
              )->addColumn(
                  'reserved5',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved5'
              )->addColumn(
                  'reserved6',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved6'
              )->addColumn(
                  'reserved7',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved7'
              )->addColumn(
                  'reserved8',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved8'
              )->addColumn(
                  'reserved9',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved9'
              )->addColumn(
                  'reserved10',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved10'
              )
              ->addColumn(
                  'numeroCip',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'numeroCip'
              )->setComment("Payme Table");
          $setup->getConnection()->createTable($table);




           /**
          * Create table 'payme_request'
          */
          $table = $setup->getConnection()
              ->newTable($setup->getTable('payme_request'))
              ->addColumn(
                  'id_log',
                  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                  null,
                  ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                  'id_log ID'
              )
              ->addColumn(
                  'purchaseOperationNumber',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseOperationNumber'
              )
              ->addColumn(
                  'purchaseAmount',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseAmount'
              )
              ->addColumn(
                  'purchaseCurrencyCode',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseCurrencyCode'
              )
              ->addColumn(
                  'language',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'language'
              )
              ->addColumn(
                  'billingFirstName',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingFirstName'
              )
              ->addColumn(
                  'billingLastName',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingLastName'
              )
              ->addColumn(
                  'billingEmail',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingEmail'
              )
              ->addColumn(
                  'billingAddress',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingAddress'
              )
              ->addColumn(
                  'billingZip',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingZip'
              )
              ->addColumn(
                  'billingState',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingState'
              )
              ->addColumn(
                  'billingCountry',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingCountry'
              )
              ->addColumn(
                  'billingPhone',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'billingPhone'
              )
              ->addColumn(
                  'shippingFirstName',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingFirstName'
              )
              ->addColumn(
                  'shippingLastName',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingLastName'
              )
              ->addColumn(
                  'shippingEmail',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingEmail'
              )
              ->addColumn(
                  'shippingAddress',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingAddress'
              )
              ->addColumn(
                  'shippingZip',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingZip'
              )
              ->addColumn(
                  'shippingCity',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingCity'
              )
              ->addColumn(
                  'shippingState',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingState'
              )
              ->addColumn(
                  'shippingCountry',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingCountry'
              )
              ->addColumn(
                  'shippingPhone',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'shippingPhone'
              )
              ->addColumn(
                  'programmingLanguage',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'programmingLanguage'
              )
              ->addColumn(
                  'userCommerce',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'userCommerce'
              )
              ->addColumn(
                  'userCodePayme',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'userCodePayme'
              )
              ->addColumn(
                  'descriptionProducts',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'descriptionProducts'
              )
              ->addColumn(
                  'purchaseVerification',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'purchaseVerification'
              )
              ->addColumn(
                  'reserved1',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved1'
              )
             ->addColumn(
                  'reserved2',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved2'
              )->addColumn(
                  'reserved3',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved3'
              )->addColumn(
                  'reserved4',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved4'
              )->addColumn(
                  'reserved5',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved5'
              )->addColumn(
                  'reserved6',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved6'
              )->addColumn(
                  'reserved7',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved7'
              )->addColumn(
                  'reserved8',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved8'
              )->addColumn(
                  'reserved9',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved9'
              )->addColumn(
                  'reserved10',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'reserved10'
              )
              ->addColumn(
                  'numeroCip',
                  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                  255,
                  ['nullable' => false, 'default' => ''],
                    'numeroCip'
              )->setComment("Payme Table Request");
          $setup->getConnection()->createTable($table);
      
       $data = [
            'scope' => 'default',
            'scope_id' => 0,
            'path' => 'payment/payme_gateway/main_parameters/callbackurl',
            'value' => 'Copyrigh',
        ];
        $setup->getConnection()
           ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
       
        $setup->endSetup();
    }
}