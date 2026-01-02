<?php

namespace DiMedia\CustomStatus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Get the quote table name
        $quoteTable = $setup->getTable('quote');

        // Add cod_fee column if it doesn't exist
        if ($setup->getConnection()->isTableExists($quoteTable) == true) {
            $setup->getConnection()->addColumn(
                $quoteTable,
                'cod_fee',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'scale' => 2,
                    'precision' => 10,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Cash on Delivery Fee'
                ]
            );
        }

        $setup->endSetup();
    }
}
