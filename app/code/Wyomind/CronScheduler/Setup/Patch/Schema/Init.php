<?php

namespace Wyomind\CronScheduler\Setup\Patch\Schema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Module\ModuleResource;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Init implements SchemaPatchInterface, PatchRevertableInterface
{
    protected SchemaSetupInterface $schemaSetup;


    const version = "1.0.0";

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup,
        ModuleResource       $moduleResource
    ) {

        $this->schemaSetup = $schemaSetup;
        $this->dbVersion = $moduleResource->getDbVersion("Wyomind_CronScheduler");
    }


    public function apply()
    {

        if (version_compare($this->dbVersion, self::version) >= 0) {
            $this->schemaSetup->getConnection()->startSetup();
            $installer = $this->schemaSetup;

            /*
                     * add column `origin` to `cron_schedule`
                     */
            $installer->getConnection()->addColumn($installer->getTable('cron_schedule'), 'origin', [
                'type' => Table::TYPE_INTEGER,
                'length' => 1,
                'nullable' => true,
                'comment' => 'Where does the schedule has been triggered? 0:Cron, 1:Backend, 2:CLI, 3:WebAPI'
            ]);

            /*
             * add column `user` to `cron_schedule`
             */
            $installer->getConnection()->addColumn($installer->getTable('cron_schedule'), 'user', [
                'type' => Table::TYPE_TEXT,
                'length' => 100,
                'nullable' => true,
                'comment' => 'Who triggered the schedule'
            ]);

            /*
             * add column `ip` to `cron_schedule`
             */
            $installer->getConnection()->addColumn($installer->getTable('cron_schedule'), 'ip', [
                'type' => Table::TYPE_TEXT,
                'length' => 40,
                'nullable' => true,
                'comment' => 'From which IP?'
            ]);

            /*
             * add column `error_file` to `cron_schedule`
             */
            $installer->getConnection()->addColumn($installer->getTable('cron_schedule'), 'error_file', [
                'type' => Table::TYPE_TEXT,
                'length' => 500,
                'nullable' => true,
                'comment' => 'Where (file) the error has been triggered?'
            ]);

            /*
             * add column `error_line` to `cron_schedule`
             */
            $installer->getConnection()->addColumn($installer->getTable('cron_schedule'), 'error_line', [
                'type' => Table::TYPE_TEXT,
                'length' => 6,
                'nullable' => true,
                'comment' => 'Where (line) the error has been triggered?'
            ]);

            $this->schemaSetup->getConnection()->endSetup();
        }
    }


    /**
     * @inheritdoc
     */
    public function revert()
    {
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
