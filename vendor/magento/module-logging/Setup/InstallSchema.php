<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Logging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        /**
         * Create table 'magento_logging_event'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_logging_event')
        )->addColumn(
            'log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Log Id'
        )->addColumn(
            'ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Ip address'
        )->addColumn(
            'x_forwarded_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Real ip address if visitor used proxy'
        )->addColumn(
            'event_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'Event Code'
        )->addColumn(
            'time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Even date'
        )->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Event action'
        )->addColumn(
            'info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Additional information'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            15,
            [],
            'Status'
        )->addColumn(
            'user',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            40,
            [],
            'User name'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'User Id'
        )->addColumn(
            'fullaction',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            200,
            [],
            'Full action description'
        )->addColumn(
            'error_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Error Message'
        )->addIndex(
            $installer->getIdxName('magento_logging_event', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('magento_logging_event', ['user']),
            ['user']
        )->addForeignKey(
            $installer->getFkName('magento_logging_event', 'user_id', 'admin_user', 'user_id'),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->setComment(
            'Enterprise Logging Event'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_logging_event_changes'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_logging_event_changes')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Enterprise logging id'
        )->addColumn(
            'source_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            [],
            'Logged Source Name'
        )->addColumn(
            'event_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Logged event id'
        )->addColumn(
            'source_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Logged Source Id'
        )->addColumn(
            'original_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Logged Original Data'
        )->addColumn(
            'result_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Logged Result Data'
        )->addIndex(
            $installer->getIdxName('magento_logging_event_changes', ['event_id']),
            ['event_id']
        )->addForeignKey(
            $installer->getFkName('magento_logging_event_changes', 'event_id', 'magento_logging_event', 'log_id'),
            'event_id',
            $installer->getTable('magento_logging_event'),
            'log_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Logging Event Changes'
        );
        $installer->getConnection()->createTable($table);
    }
}
