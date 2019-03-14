<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedSalesRule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\AdvancedRule\Model\Condition\Filter;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'magento_salesrule_filter'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_salesrule_filter'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Group Id'
            )
            ->addColumn(
                'weight',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['default' => 1.0000, 'nullable' => false],
                'Condition weight'
            )
            ->addColumn(
                Filter::KEY_FILTER_TEXT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Filter text'
            )
            ->addColumn(
                Filter::KEY_FILTER_TEXT_GENERATOR_CLASS,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Filter text generator class name'
            )
            ->addColumn(
                Filter::KEY_FILTER_TEXT_GENERATOR_ARGUMENTS,
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Filter text generator arguments'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_salesrule_filter',
                    [Filter::KEY_FILTER_TEXT_GENERATOR_CLASS, Filter::KEY_FILTER_TEXT_GENERATOR_ARGUMENTS],
                    true
                ),
                [Filter::KEY_FILTER_TEXT_GENERATOR_CLASS, Filter::KEY_FILTER_TEXT_GENERATOR_ARGUMENTS]
            )
            ->addIndex(
                $setup->getIdxName('magento_salesrule_filter', ['rule_id']),
                ['rule_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_salesrule_filter', [Filter::KEY_FILTER_TEXT, 'rule_id', 'group_id']),
                [Filter::KEY_FILTER_TEXT, 'rule_id', 'group_id']
            )
            ->setComment('Enterprise SalesRule Filter');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
