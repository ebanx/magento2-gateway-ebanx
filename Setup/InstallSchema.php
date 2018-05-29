<?php
namespace DigitalHub\Ebanx\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
	{
		$installer = $setup;
		$installer->startSetup();

		if (!$installer->tableExists('ebanx_creditcard_token')) {
			$table = $installer->getConnection()->newTable($installer->getTable('ebanx_creditcard_token'))
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Token ID'
				)
				->addColumn(
					'customer_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['nullable => false'],
					'Customer ID'
				)
				->addColumn(
					'token',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Post URL Key'
				)
				->addColumn(
					'payment_method',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Ebanx Payment Method'
				)
				->addColumn(
					'payment_type_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					45,
					[],
					'Credit Card Type Code'
				)
				->addColumn(
					'masked_card_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					45,
					[],
					'Masked Card Number'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created At'
				);
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}
