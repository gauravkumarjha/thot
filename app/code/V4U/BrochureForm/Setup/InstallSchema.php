<?php

namespace V4U\BrochureForm\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$setup->tableExists('v4u_brochure_form')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('v4u_brochure_form')
            )
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'ID')
                ->addColumn('fullName', Table::TYPE_TEXT, 255, [], 'Full Name')
                ->addColumn('email', Table::TYPE_TEXT, 255, [], 'Email')
                ->addColumn('phone', Table::TYPE_TEXT, 50, [], 'Phone')
                ->addColumn('organization', Table::TYPE_TEXT, 255, [], 'Organization')
                ->addColumn('city', Table::TYPE_TEXT, 100, [], 'City')
                ->addColumn('country', Table::TYPE_TEXT, 100, [], 'Country')
                ->addColumn('state', Table::TYPE_TEXT, 100, [], 'State')
                ->addColumn('product', Table::TYPE_TEXT, 255, [], 'Product')
                ->addColumn('message', Table::TYPE_TEXT, '2M', [], 'Message')
                ->addColumn('ip_address', Table::TYPE_TEXT, 100, [], 'IP Address')
                ->addColumn('submitted_at', Table::TYPE_DATETIME, null, [], 'Submitted At')
                ->setComment('Brochure Form Submission Table');

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
