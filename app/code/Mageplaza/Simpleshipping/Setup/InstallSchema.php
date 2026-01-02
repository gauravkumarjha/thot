<?php

namespace Mageplaza\Simpleshipping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$setup->tableExists('sales_order_item')) {
            $table = $setup->getTable('sales_order_item');
            $setup->getConnection()->addColumn(
                $table,
                'custom_shipping_price',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Custom Shipping Price'
                ]
            );
        }

        $setup->endSetup();
    }
}
