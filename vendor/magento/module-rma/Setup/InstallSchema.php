<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var string
     */
    private static $salesConnectionName = 'sales';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database before module installation
         */
        $setup->startSetup();

        /**
         * Create table 'magento_rma'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'RMA Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Active'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Increment Id'
        )->addColumn(
            'date_requested',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'RMA Requested At'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Order Increment Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'customer_custom_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Custom Email'
        )->addColumn(
            'protect_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Protect Code'
        )->addIndex(
            $setup->getIdxName('magento_rma', ['status']),
            ['status']
        )->addIndex(
            $setup->getIdxName('magento_rma', ['is_active']),
            ['is_active']
        )->addIndex(
            $setup->getIdxName(
                'magento_rma',
                ['increment_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma', ['date_requested']),
            ['date_requested']
        )->addIndex(
            $setup->getIdxName('magento_rma', ['order_id']),
            ['order_id']
        )->addIndex(
            $setup->getIdxName('magento_rma', ['order_increment_id']),
            ['order_increment_id']
        )->addIndex(
            $setup->getIdxName('magento_rma', ['store_id']),
            ['store_id']
        )->addIndex(
            $setup->getIdxName('magento_rma', ['customer_id']),
            ['customer_id']
        )->addForeignKey(
            $setup->getFkName('magento_rma', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $setup->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $setup->getFkName('magento_rma', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->setComment(
            'RMA LIst'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_grid'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_grid')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'RMA Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Increment Id'
        )->addColumn(
            'date_requested',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'RMA Requested At'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Order Increment Id'
        )->addColumn(
            'order_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Order Created At'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Billing Name'
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['status']),
            ['status']
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_grid',
                ['increment_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['date_requested']),
            ['date_requested']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['order_id']),
            ['order_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['order_increment_id']),
            ['order_increment_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['order_date']),
            ['order_date']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['store_id']),
            ['store_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_grid', ['customer_name']),
            ['customer_name']
        )->addForeignKey(
            $setup->getFkName('magento_rma_grid', 'entity_id', 'magento_rma', 'entity_id'),
            'entity_id',
            $setup->getTable('magento_rma'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Grid'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_status_history'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_status_history')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'rma_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'RMA Entity Id'
        )->addColumn(
            'is_customer_notified',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Is Customer Notified'
        )->addColumn(
            'is_visible_on_front',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Visible On Front'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Comment'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'is_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Is this Merchant Comment'
        )->addIndex(
            $setup->getIdxName('magento_rma_status_history', ['rma_entity_id']),
            ['rma_entity_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_status_history', ['created_at']),
            ['created_at']
        )->addForeignKey(
            $setup->getFkName('magento_rma_status_history', 'rma_entity_id', 'magento_rma', 'entity_id'),
            'rma_entity_id',
            $setup->getTable('magento_rma'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA status history magento_rma_status_history'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_entity'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'rma_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'RMA entity id'
        )->addColumn(
            'is_qty_decimal',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Qty Decimal'
        )->addColumn(
            'qty_requested',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Qty of requested for RMA items'
        )->addColumn(
            'qty_authorized',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Qty of authorized items'
        )->addColumn(
            'qty_approved',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Qty of approved items'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'order_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Order Item Id'
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Name'
        )->addColumn(
            'qty_returned',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Qty of returned items'
        )->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Sku'
        )->addColumn(
            'product_admin_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Name For Backend'
        )->addColumn(
            'product_admin_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Product Sku For Backend'
        )->addColumn(
            'product_options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Product Options'
        )->addForeignKey(
            $setup->getFkName('magento_rma_item_entity', 'rma_entity_id', 'magento_rma', 'entity_id'),
            'rma_entity_id',
            $setup->getTable('magento_rma'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_eav_attribute'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_eav_attribute')
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addColumn(
            'is_visible',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Visible'
        )->addColumn(
            'input_filter',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Input Filter'
        )->addColumn(
            'multiline_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Multiline Count'
        )->addColumn(
            'validate_rules',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Validate Rules'
        )->addColumn(
            'is_system',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is System'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order'
        )->addColumn(
            'data_model',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Data Model'
        )->addForeignKey(
            $setup->getFkName('magento_rma_item_eav_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item EAV Attribute'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'customer_entity_datetime'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity_datetime')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Value'
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_item_entity_datetime',
                ['entity_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_datetime', ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_datetime', ['entity_id', 'attribute_id', 'value']),
            ['entity_id', 'attribute_id', 'value']
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_datetime',
                'attribute_id',
                'eav_attribute',
                'attribute_id'
            ),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_datetime',
                'entity_id',
                'magento_rma_item_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('magento_rma_item_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity Datetime'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_entity_decimal'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity_decimal')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Value'
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_item_entity_decimal',
                ['entity_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_decimal', ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_decimal', ['entity_id', 'attribute_id', 'value']),
            ['entity_id', 'attribute_id', 'value']
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_decimal',
                'attribute_id',
                'eav_attribute',
                'attribute_id'
            ),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_decimal',
                'entity_id',
                'magento_rma_item_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('magento_rma_item_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity Decimal'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_entity_int'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity_int')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Value'
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_item_entity_int',
                ['entity_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_int', ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_int', ['entity_id', 'attribute_id', 'value']),
            ['entity_id', 'attribute_id', 'value']
        )->addForeignKey(
            $setup->getFkName('magento_rma_item_entity_int', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_int',
                'entity_id',
                'magento_rma_item_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('magento_rma_item_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity Int'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_entity_text'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity_text')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Value'
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_item_entity_text',
                ['entity_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_text', ['attribute_id']),
            ['attribute_id']
        )->addForeignKey(
            $setup->getFkName('magento_rma_item_entity_text', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_text',
                'entity_id',
                'magento_rma_item_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('magento_rma_item_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity Text'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_entity_varchar'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_entity_varchar')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Value Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Id'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Entity Id'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Value'
        )->addIndex(
            $setup->getIdxName(
                'magento_rma_item_entity_varchar',
                ['entity_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_varchar', ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $setup->getIdxName('magento_rma_item_entity_varchar', ['entity_id', 'attribute_id', 'value']),
            ['entity_id', 'attribute_id', 'value']
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_varchar',
                'attribute_id',
                'eav_attribute',
                'attribute_id'
            ),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_entity_varchar',
                'entity_id',
                'magento_rma_item_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('magento_rma_item_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Entity Varchar'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_form_attribute'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_form_attribute')
        )->addColumn(
            'form_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false, 'primary' => true],
            'Form Code'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addIndex(
            $setup->getIdxName('magento_rma_item_form_attribute', ['attribute_id']),
            ['attribute_id']
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_form_attribute',
                'attribute_id',
                'eav_attribute',
                'attribute_id'
            ),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'RMA Item Form Attribute'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_rma_item_eav_attribute_website'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_item_eav_attribute_website')
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addColumn(
            'is_visible',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Is Visible'
        )->addColumn(
            'is_required',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Is Required'
        )->addColumn(
            'default_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Default Value'
        )->addColumn(
            'multiline_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Multiline Count'
        )->addIndex(
            $setup->getIdxName('magento_rma_item_eav_attribute_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_eav_attribute_website',
                'attribute_id',
                'eav_attribute',
                'attribute_id'
            ),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'magento_rma_item_eav_attribute_website',
                'website_id',
                'store_website',
                'website_id'
            ),
            'website_id',
            $setup->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise RMA Item Eav Attribute Website'
        );
        $setup->getConnection()->createTable($table);

        //TODO: should be refactored in order to avoid sales table modification
        $setup->getConnection(self::$salesConnectionName)->addColumn(
            $setup->getTable('sales_order_item', self::$salesConnectionName),
            'qty_returned',
            [
                'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'SCALE' => 4,
                'PRECISION' => 12,
                'DEFAULT' => '0.0000',
                'NULLABLE' => false,
                'COMMENT' => 'Qty of returned items'
            ]
        );

        /**
         * Create table 'magento_rma_shipping_label'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('magento_rma_shipping_label')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'rma_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'RMA Entity Id'
        )->addColumn(
            'shipping_label',
            \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
            '2M',
            [],
            'Shipping Label Content'
        )->addColumn(
            'packages',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20000,
            [],
            'Packed Products in Packages'
        )->addColumn(
            'track_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Tracking Number'
        )->addColumn(
            'carrier_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Carrier Title'
        )->addColumn(
            'method_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Method Title'
        )->addColumn(
            'carrier_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Carrier Code'
        )->addColumn(
            'method_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Method Code'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Price'
        )->addColumn(
            'is_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            6,
            [],
            'Is this Label Created by Merchant'
        )->addForeignKey(
            $setup->getFkName('magento_rma_shipping_label', 'rma_entity_id', 'magento_rma', 'entity_id'),
            'rma_entity_id',
            $setup->getTable('magento_rma'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'List of RMA Shipping Labels'
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
