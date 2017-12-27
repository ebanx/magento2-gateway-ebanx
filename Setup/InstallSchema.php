<?php

namespace Ebanx\Payments\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    private $eavSetupFactory;


    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        /**
         * Create table 'ebanx_payments'
         */
        $connection = $setup->getConnection();

        $this->createEbanxPaymentsTable($setup, $connection);
        $this->createDocumentColumn($setup, $connection);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param AdapterInterface     $connection
     */
    private function createEbanxPaymentsTable(SchemaSetupInterface $setup, AdapterInterface $connection)
    {
        if ($connection->isTableExists('ebanx_payments')) {
            return;
        }

        $table = $connection
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
                'order_id',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
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
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param AdapterInterface     $connection
     */
    private function createDocumentColumn(SchemaSetupInterface $setup, AdapterInterface $connection)
    {
        if ($connection->isTableExists('ebanx_costumer_document')) {
            return;
        }

        $table = $connection
            ->newTable($setup->getTable('ebanx_costumer_document'))
            ->addColumn(
                'costumer_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'document',
                Table::TYPE_TEXT,
                16,
                ['nullable' => false]
            );
        $connection->createTable($table);
    }
}
