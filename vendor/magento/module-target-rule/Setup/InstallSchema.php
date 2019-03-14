<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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
         * Create table 'magento_targetrule'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )
            ->addColumn(
                'from_date',
                Table::TYPE_DATE,
                null,
                [],
                'From'
            )
            ->addColumn(
                'to_date',
                Table::TYPE_DATE,
                null,
                [],
                'To'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '64K',
                ['nullable' => false],
                'Conditions Serialized'
            )
            ->addColumn(
                'actions_serialized',
                Table::TYPE_TEXT,
                '64K',
                ['nullable' => false],
                'Actions Serialized'
            )
            ->addColumn(
                'positions_limit',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Positions Limit'
            )
            ->addColumn(
                'apply_to',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Apply To'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                [],
                'Sort Order'
            )
            ->addColumn(
                'action_select',
                Table::TYPE_TEXT,
                '64K',
                [],
                'Action Select'
            )
            ->addColumn(
                'action_select_bind',
                Table::TYPE_TEXT,
                '64K',
                [],
                'Action Select Bind'
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule', ['is_active']),
                ['is_active']
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule', ['apply_to']),
                ['apply_to']
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule', ['sort_order']),
                ['sort_order']
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule', ['from_date', 'to_date']),
                ['from_date', 'to_date']
            )
            ->setComment('Enterprise Targetrule');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_customersegment'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_customersegment'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'segment_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Segment Id'
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule_customersegment', ['segment_id']),
                ['segment_id']
            )
            ->addForeignKey(
                $setup->getFkName('magento_targetrule_customersegment', 'rule_id', 'magento_targetrule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_targetrule'),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_targetrule_customersegment',
                    'segment_id',
                    'magento_customersegment_segment',
                    'segment_id'
                ),
                'segment_id',
                $setup->getTable('magento_customersegment_segment'),
                'segment_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Targetrule Customersegment');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_product'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Id'
            )
            ->addIndex(
                $setup->getIdxName('magento_targetrule_product', ['product_id']),
                ['product_id']
            )
            ->addForeignKey(
                $setup->getFkName('magento_targetrule_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('magento_targetrule_product', 'rule_id', 'magento_targetrule', 'rule_id'),
                'rule_id',
                $setup->getTable('magento_targetrule'),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Targetrule Product');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Group Id'
            )
            ->addColumn(
                'type_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Type Id'
            )
            ->addColumn(
                'flag',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Flag'
            )
            ->addColumn(
                'customer_segment_id',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0', 'primary' => true],
                'Customer Segment Id'
            )
            ->setComment('Enterprise Targetrule Index');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_related'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_related'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'customer_segment_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Segment Id'
            )
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Set Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_related',
                    [
                        'entity_id',
                        'store_id',
                        'customer_group_id',
                        'customer_segment_id'
                    ]
                ),
                [
                    'entity_id',
                    'store_id',
                    'customer_group_id',
                    'customer_segment_id'
                ],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Enterprise Targetrule Index Related');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_upsell'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_upsell'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'customer_segment_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Segment Id'
            )
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Set Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_upsell',
                    [
                        'entity_id',
                        'store_id',
                        'customer_group_id',
                        'customer_segment_id'
                    ]
                ),
                [
                    'entity_id',
                    'store_id',
                    'customer_group_id',
                    'customer_segment_id'
                ],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Enterprise Targetrule Index Upsell');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_crosssell'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_crosssell'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'customer_segment_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Segment Id'
            )
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product Set Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_crosssell',
                    [
                        'entity_id',
                        'store_id',
                        'customer_group_id',
                        'customer_segment_id'
                    ]
                ),
                [
                    'entity_id',
                    'store_id',
                    'customer_group_id',
                    'customer_segment_id'
                ],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Enterprise Targetrule Index Crosssell');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_crosssell_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_crosssell_product'))
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'TargetRule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Product Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_crosssell_product',
                    ['product_set_id', 'product_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_set_id', 'product_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_targetrule_index_crosssell_product',
                    'product_set_id',
                    'magento_targetrule_index_crosssell',
                    'product_set_id'
                ),
                'product_set_id',
                $setup->getTable('magento_targetrule_index_crosssell'),
                'product_set_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Targetrule Index Crosssell Products');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_related_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_related_product'))
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'TargetRule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Product Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_related_product',
                    ['product_set_id', 'product_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_set_id', 'product_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_targetrule_index_related_product',
                    'product_set_id',
                    'magento_targetrule_index_related',
                    'product_set_id'
                ),
                'product_set_id',
                $setup->getTable('magento_targetrule_index_related'),
                'product_set_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Targetrule Index Related Products');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_targetrule_index_upsell_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_targetrule_index_upsell_product'))
            ->addColumn(
                'product_set_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'TargetRule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Product Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_targetrule_index_upsell_product',
                    ['product_set_id', 'product_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_set_id', 'product_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_targetrule_index_upsell_product',
                    'product_set_id',
                    'magento_targetrule_index_upsell',
                    'product_set_id'
                ),
                'product_set_id',
                $setup->getTable('magento_targetrule_index_upsell'),
                'product_set_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Targetrule Index Upsell Products');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
