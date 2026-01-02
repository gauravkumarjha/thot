<?php

namespace DiMedia\ShippingDayUnicomerce\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        /**
         * Add columns to sales_order
         */
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $orderTable = $setup->getTable('sales_order');

            $orderColumns = [
                'fulfillment_date' => [
                    'type' => Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Fulfillment Date'
                ],
                'store_code' => [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Store Code'
                ]
            ];

            foreach ($orderColumns as $name => $definition) {
                if (!$connection->tableColumnExists($orderTable, $name)) {
                    $connection->addColumn($orderTable, $name, $definition);
                }
            }
        }

        /**
         * Add fulfillment_date to sales_order_item
         */
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $orderItemTable = $setup->getTable('sales_order_item');

            if (!$connection->tableColumnExists($orderItemTable, 'fulfillment_date')) {
                $connection->addColumn(
                    $orderItemTable,
                    'fulfillment_date',
                    [
                        'type' => Table::TYPE_DATE,
                        'nullable' => true,
                        'comment' => 'Fulfillment Date'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
