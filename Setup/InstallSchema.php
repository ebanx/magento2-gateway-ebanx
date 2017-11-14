<?php

namespace Ebanx\Payments\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install( SchemaSetupInterface $setup, ModuleContextInterface $context ) {

        $setup->startSetup();
        /**
         * Create table 'ebanx_payments'
         */
        $connection = $setup->getConnection();
        if ($connection->isTableExists('ebanx_payments')){
            $setup->endSetup();
            return;
        }

        $table      = $connection
            ->newTable($setup->getTable('ebanx_payments'))
            ->addColumn(
                'payment_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'payment_hash',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
            )
            ->addColumn(
                'sales_order_entity_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true]
            )
            ->addColumn(
                'due_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'bar_code',
                Table::TYPE_TEXT,
                47,
                ['default' => null]
            )
            ->addColumn(
                'instalments',
                Table::TYPE_INTEGER,
                2,
                ['nullable' => false]
            )
            ->addColumn(
                'environment',
                Table::TYPE_TEXT,
                7,
                ['nullable' => false]
            )
            ->addColumn(
                'customer_document',
                Table::TYPE_TEXT,
                14,
                ['nullable' => false]
            )
            ->addColumn(
                'local_amount',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => false]
            );
        $connection->createTable($table);
        $connection
              ->addForeignKey(
                  $setup->getFkName('ebanx_payments', 'sales_order_entity_id', 'sales_order', 'entity_id'),
                  'ebanx_payments',
                  'sales_order_entity_id',
                  'sales_order',
                  'entity_id',
                  Table::ACTION_CASCADE
              );
        $setup->endSetup();
    }
}
