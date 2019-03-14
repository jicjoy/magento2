<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reminder\Setup;

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
        $setup->startSetup();

        /**
         * Create table 'magento_reminder_rule'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_reminder_rule'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Name'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            ->addColumn(
                'conditions_serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Conditions Serialized'
            )
            ->addColumn(
                'condition_sql',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Condition Sql'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'salesrule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Salesrule Id'
            )
            ->addColumn(
                'schedule',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Schedule'
            )
            ->addColumn(
                'default_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Default Label'
            )
            ->addColumn(
                'default_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Default Description'
            )
            ->addColumn(
                'from_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true, 'default' => null],
                'Active From'
            )
            ->addColumn(
                'to_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true, 'default' => null],
                'Active To'
            )
            ->addIndex(
                $setup->getIdxName('magento_reminder_rule', ['salesrule_id']),
                ['salesrule_id']
            )
            ->setComment('Enterprise Reminder Rule');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_reminder_rule_website'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_reminder_rule_website'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Website Id'
            )
            ->addIndex(
                $setup->getIdxName('magento_reminder_rule_website', ['website_id']),
                ['website_id']
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_rule_website', 'rule_id', 'magento_reminder_rule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_reminder_rule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_rule_website', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $setup->getTable('store_website'),
                'website_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Reminder Rule Website');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_reminder_template'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_reminder_template'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Template ID'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Label'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            ->addIndex(
                $setup->getIdxName('magento_reminder_template', ['template_id']),
                ['template_id']
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_template', 'template_id', 'email_template', 'template_id'),
                'template_id',
                $setup->getTable('email_template'),
                'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_template', 'rule_id', 'magento_reminder_rule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_reminder_rule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Reminder Template');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_reminder_rule_coupon'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_reminder_rule_coupon'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'coupon_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Coupon Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Id'
            )
            ->addColumn(
                'associated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Associated At'
            )
            ->addColumn(
                'emails_failed',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Emails Failed'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Is Active'
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_rule_coupon', 'rule_id', 'magento_reminder_rule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_reminder_rule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Reminder Rule Coupon');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_reminder_rule_log'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_reminder_rule_log'))
            ->addColumn(
                'log_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Log Id'
            )
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Id'
            )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Sent At'
            )
            ->addIndex(
                $setup->getIdxName('magento_reminder_rule_log', ['rule_id']),
                ['rule_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_reminder_rule_log', ['customer_id']),
                ['customer_id']
            )
            ->addForeignKey(
                $setup->getFkName('magento_reminder_rule_log', 'rule_id', 'magento_reminder_rule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_reminder_rule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Reminder Rule Log');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
