<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableMenu = $installer->getTable('ves_megamenu_menu');
        $tableItems = $installer->getTable('ves_megamenu_item');

        $installer->getConnection()->addColumn(
            $tableItems,
            'hover_icon',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Hover Icon'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgcolor',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Color'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimagerepeat',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image Repeat'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgpositionx',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Position X'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgpositiony',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Position Y'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_inlinecss',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Inline CSS'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'parentcat',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Parent Category'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'animation_in',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Animation In'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'animation_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Animation Time'
            ]
        );

        $installer->getConnection()->modifyColumn(
            $tableItems,
            'id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                'auto_increment' => true,
                'primary' => true,
                'nullable' => false
            ]
            );

        $tableMenu = $installer->getTable('ves_megamenu_menu');
        $installer->getConnection()->addColumn(
            $tableMenu,
            'desktop_template',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Desktop Template'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'disable_iblocks',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Disable Item Blocks'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'event',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Event'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableMenu,
            'classes',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Classes'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableMenu,
            'width',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Width'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'scrolltofixed',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Scroll to fixed'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'current_version',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Current Version'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'mobile_menu_alias',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Mobile menu alias'
            ]
        );
        $installer->endSetup();



        /**
         * Create table 'ves_megamenu_menu_customergroup'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu_customergroup')
        )->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Menu ID'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group ID'
        )->addIndex(
            $installer->getIdxName('ves_megamenu_menu_customergroup', ['customer_group_id']),
            ['customer_group_id']
        )->setComment(
            'Menu Custom Group'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'ves_megamenu_menu_log'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu_log')
        )->addColumn(
            'log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true, 'identity' => true],
            'Log ID'
        )->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Menu ID'
        )->addColumn(
            'version',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['unsigned' => true],
            'Menu Data'
        )->addColumn(
            'menu_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['unsigned' => true],
            'Menu Data'
        )->addColumn(
            'menu_structure',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['unsigned' => true],
            'Menu Structure'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['unsigned' => true],
            'Menu Note'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Menu Modification Time'
        )->setComment(
            'Menu Log'
        );

        //Update for version 1.1.3
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            //$foreignKeys = $this->getForeignKeys($installer);
            //$this->dropForeignKeys($installer, $foreignKeys);
            //$this->alterTables($installer, $foreignKeys);
            //$this->createForeignKeys($installer, $foreignKeys);

            $installer->getConnection()->modifyColumn(
                $installer->getTable('ves_megamenu_menu_customergroup'),
                'customer_group_id',
                [
                    'type' => 'integer',
                    'unsigned' => true,
                    'nullable' => false
                ]
            );
            /*
            Alter table add foreign key

            $installer->getConnection()->addForeignKey(
                $key['FK_NAME'],
                $key['TABLE_NAME'],
                $key['COLUMN_NAME'],
                $key['REF_TABLE_NAME'],
                $key['REF_COLUMN_NAME'],
                $key['ON_DELETE']
            );

            */
            /*$installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('ves_megamenu_menu_customergroup', 'menu_id', 'ves_megamenu_menu', 'menu_id'),
                    $installer->getTable('ves_megamenu_menu_customergroup'),
                    'menu_id',
                    $installer->getTable('ves_megamenu_menu'),
                    'menu_id',
                    Table::ACTION_CASCADE
                );*/
        }
        //Update for version 1.1.4
        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $installer->getConnection()->addColumn(
                $tableItems,
                'cms_page',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'comment' => 'Cms Page Identifier'
                ]
            );
            $installer->getConnection()->addColumn(
                $tableMenu,
                'disable_above',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Disable Above'
                ]
            );
        }

        /** update database tables for module version 1.1.6 */
        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $installer->getConnection()->modifyColumn(
                $installer->getTable('ves_megamenu_menu'),
                'structure',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '30M',
                    'nullable' => false,
                    'comment' => 'Structure'
                ]
            );
            $installer->getConnection()->modifyColumn(
                $installer->getTable('ves_megamenu_menu'),
                'html',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '30M',
                    'nullable' => false,
                    'comment' => 'Html'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('ves_megamenu_menu'),
                'design',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '30M',
                    'comment' => 'Design'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('ves_megamenu_menu'),
                'params',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '30M',
                    'comment' => 'Params'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('ves_megamenu_menu'),
                'custom_css',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '30M',
                    'nullable' => true,
                    'comment' => 'Custom Css Code For megamenu profile'
                ]
            );

            // Update megamenu item table
            $tableItems = $installer->getTable('ves_megamenu_item');

            $installer->getConnection()->addColumn(
                $tableItems,
                'tab_position',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Tab Position'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'before_html',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Before Html'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'after_html',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'After Html'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'caret',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Caret'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'hover_caret',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Hover Caret'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'sub_height',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Sub Height'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'caret',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Caret'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'hover_caret',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Hover Caret'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'child_col_type',
                [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 50,
                    'comment'  => 'Child Column type: normal or bootstrap'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'submenu_sorttype',
                [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 50,
                    'comment'  => 'Sub menu sort type'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableItems,
                'isgroup_level',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'length' => 4,
                    'comment'  => 'Is Group Level for sub menu items with type parent category'
                ]
            );

            /**
             * Create table 'ves_megamenu_menu_log'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('ves_megamenu_cache')
            )->addColumn(
                'cache_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'primary' => true, 'identity' => true],
                'Cache ID'
            )->addColumn(
                'menu_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Menu ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )->addColumn(
                'html',
                Table::TYPE_TEXT,
                '30M',
                ['unsigned' => true],
                'Menu Html'
            )->addColumn(
                'creation_time',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Menu Creation Time'
            )->addIndex(
                $installer->getIdxName('ves_megamenu_cache', ['menu_id']),
                ['menu_id']
            )->addIndex(
                $installer->getIdxName('ves_megamenu_cache', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('ves_megamenu_cache', 'menu_id', 'ves_megamenu_menu', 'menu_id'),
                'menu_id',
                $installer->getTable('ves_megamenu_menu'),
                'menu_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('ves_megamenu_cache', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Menu Log'
            );
            $installer->getConnection()->createTable($table);
        }
        $installer->getConnection()->createTable($table);
    }
    /**
     * @param SchemaSetupInterface $setup
     * @param array $keys
     * @return void
     */
    private function dropForeignKeys(SchemaSetupInterface $setup, array $keys)
    {
        foreach ($keys as $key) {
            $setup->getConnection()->dropForeignKey($key['TABLE_NAME'], $key['FK_NAME']);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param array $keys
     * @return void
     */
    private function createForeignKeys(SchemaSetupInterface $setup, array $keys)
    {
        foreach ($keys as $key) {
            $setup->getConnection()->addForeignKey(
                $key['FK_NAME'],
                $key['TABLE_NAME'],
                $key['COLUMN_NAME'],
                $key['REF_TABLE_NAME'],
                $key['REF_COLUMN_NAME'],
                $key['ON_DELETE']
            );
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return array
     */
    private function getForeignKeys(SchemaSetupInterface $setup)
    {
        $foreignKeys = [];
        $keysTree = $setup->getConnection()->getForeignKeysTree();
        foreach ($keysTree as $indexes) {
            foreach ($indexes as $index) {
                if ($index['REF_TABLE_NAME'] == $setup->getTable('ves_megamenu_menu_customergroup')
                    && $index['REF_COLUMN_NAME'] == 'customer_group_id'
                ) {
                    $foreignKeys[] = $index;
                }
            }
        }
        return $foreignKeys;
    }
}
