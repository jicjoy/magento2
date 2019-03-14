<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * DB schema installer for a module
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install DB schema for a module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'support_backup'
         */
        $table = $setup->getConnection()
            ->newTable(
                $setup->getTable('support_backup')
            )
            ->addColumn(
                'backup_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Backup ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default'  => '0',
                    'unsigned' => true,
                ],
                'Status'
            )
            ->addColumn(
                'last_update',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default'  => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                ],
                'Last Updated'
            )
            ->addColumn(
                'log',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [
                    'default' => null
                ],
                'Log'
            )
            ->addIndex(
                $setup->getIdxName('support_backup', ['status']),
                ['status']
            )
            ->setComment('Support System Backups');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'support_backup_item'
         */
        $table = $setup->getConnection()
            ->newTable(
                $setup->getTable('support_backup_item')
            )
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Item ID'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default'  => '0',
                    'unsigned' => true,
                ],
                'Status'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'nullable'  => false,
                    'default'   => '0',
                    'unsigned'  => true,
                ],
                'Type'
            )
            ->addColumn(
                'size',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                [
                    'nullable' => false,
                    'default'  => '0',
                    'unsigned' => true,
                ],
                'Size'
            )
            ->addColumn(
                'backup_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Backup ID'
            )
            ->addIndex(
                $setup->getIdxName('support_backup_item', ['status']),
                ['status']
            )
            ->addIndex(
                $setup->getIdxName('support_backup_item', ['type']),
                ['type']
            )
            ->addForeignKey(
                $setup->getFkName('support_backup', 'backup_id', 'support_backup_item', 'backup_id'),
                'backup_id',
                $setup->getTable('support_backup'),
                'backup_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Support System Backup Items');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'support_report'
         */
        $table = $setup->getConnection()
            ->newTable(
                $setup->getTable('support_report')
            )
            ->addColumn(
                'report_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Report ID'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->addColumn(
                'client_host',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Client Host'
            )
            ->addColumn(
                'magento_version',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                'Magento'
            )
            ->addColumn(
                'report_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '4k',
                ['nullable' => false],
                'Report Groups'
            )
            ->addColumn(
                'report_flags',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Report Flags'
            )
            ->addColumn(
                'report_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1536k',
                ['nullable' => false],
                'Report Data'
            )
            ->setComment('Support System Reports');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
