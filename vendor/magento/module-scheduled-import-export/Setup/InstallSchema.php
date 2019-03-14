<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ScheduledImportExport\Setup;

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

        /**
         * Create table 'magento_scheduled_operations'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_scheduled_operations'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Operation Name'
            )
            ->addColumn(
                'operation_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Operation'
            )
            ->addColumn(
                'entity_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Entity'
            )
            ->addColumn(
                'behavior',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                15,
                ['nullable' => true],
                'Behavior'
            )
            ->addColumn(
                'start_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Start Time'
            )
            ->addColumn(
                'freq',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1,
                ['nullable' => false],
                'Frequency'
            )
            ->addColumn(
                'force_import',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => '0'],
                'Force Import'
            )
            ->addColumn(
                'file_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'File Information'
            )
            ->addColumn(
                'details',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Operation Details'
            )
            ->addColumn(
                'entity_attributes',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Entity Attributes'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'is_success',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 2],
                'Is Success'
            )
            ->addColumn(
                'last_run_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Last Run Date'
            )
            ->addColumn(
                'email_receiver',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Email Receiver'
            )
            ->addColumn(
                'email_sender',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Email Receiver'
            )
            ->addColumn(
                'email_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['nullable' => false],
                'Email Template'
            )
            ->addColumn(
                'email_copy',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Email Copy'
            )
            ->addColumn(
                'email_copy_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Email Copy Method'
            )
            ->setComment('Scheduled Import/Export Table');

        $setup->getConnection()->createTable($table);
    }
}
