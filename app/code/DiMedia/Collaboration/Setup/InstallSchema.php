<?php

namespace DiMedia\Collaboration\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable('collaboration_form'))
        ->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )
        ->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )
        ->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )
        ->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Phone'
        )
        ->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )
        ->addColumn(
            'path',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'File Path'
        )
        ->addColumn(
            'message',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Message'
        )
        ->setComment('Collaboration Form Table');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
