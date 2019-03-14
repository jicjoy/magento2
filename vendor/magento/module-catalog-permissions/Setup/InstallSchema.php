<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\CatalogPermissions\Model\Indexer\AbstractAction;

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
         * Create table 'magento_catalogpermissions'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogpermissions'))
            ->addColumn(
                'permission_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Permission Id'
            )
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Category Id'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Website Id'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Customer Group Id'
            )
            ->addColumn(
                'grant_catalog_category_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Grant Catalog Category View'
            )
            ->addColumn(
                'grant_catalog_product_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Grant Catalog Product Price'
            )
            ->addColumn(
                'grant_checkout_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Grant Checkout Items'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_catalogpermissions',
                    ['category_id', 'website_id', 'customer_group_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['category_id', 'website_id', 'customer_group_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_catalogpermissions',
                    'customer_group_id',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group_id',
                $setup->getTable('customer_group'),
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('magento_catalogpermissions', 'website_id', 'store_website', 'website_id'),
                'website_id',
                $setup->getTable('store_website'),
                'website_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Catalogpermissions');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_catalogpermissions_index'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogpermissions_index'))
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Category Id'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'grant_catalog_category_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Category View'
            )
            ->addColumn(
                'grant_catalog_product_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Product Price'
            )
            ->addColumn(
                'grant_checkout_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Checkout Items'
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_catalogpermissions_index',
                    ['category_id', 'website_id', 'customer_group_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['category_id', 'website_id', 'customer_group_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Enterprise Catalogpermissions Index');

        $setup->getConnection()
            ->createTable($table);

        /**
         * Create table 'magento_catalogpermissions_index_product'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogpermissions_index_product'))
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'grant_catalog_category_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Category View'
            )
            ->addColumn(
                'grant_catalog_product_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Product Price'
            )
            ->addColumn(
                'grant_checkout_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Checkout Items'
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index_product', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index_product', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_catalogpermissions_index_product',
                    ['product_id', 'store_id', 'customer_group_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_id', 'store_id', 'customer_group_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Enterprise Catalogpermissions Index Product');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_catalogpermissions_index_tmp'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogpermissions_index_tmp'))
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Category Id'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'grant_catalog_category_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Category View'
            )
            ->addColumn(
                'grant_catalog_product_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Product Price'
            )
            ->addColumn(
                'grant_checkout_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Checkout Items'
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_catalogpermissions_index',
                    ['category_id', 'website_id', 'customer_group_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['category_id', 'website_id', 'customer_group_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Catalog Category Permissions Temporary Index');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_catalogpermissions_index_product_tmp'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogpermissions_index_product_tmp'))
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'grant_catalog_category_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Category View'
            )
            ->addColumn(
                'grant_catalog_product_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Catalog Product Price'
            )
            ->addColumn(
                'grant_checkout_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Grant Checkout Items'
            )
            ->addIndex(
                $setup->getIdxName(
                    'magento_catalogpermissions_index_product_tmp',
                    ['product_id', 'store_id', 'customer_group_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['product_id', 'store_id', 'customer_group_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index_product_tmp', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogpermissions_index_product_tmp', ['customer_group_id']),
                ['customer_group_id']
            )
            ->setComment('Catalog Product Permissions Temporary Index');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();

        $this->addReplicaTable($setup, AbstractAction::INDEX_TABLE);
        $this->addReplicaTable($setup, AbstractAction::INDEX_TABLE . AbstractAction::PRODUCT_SUFFIX);
    }

    /**
     * Add replica table for existing one.
     *
     * @param SchemaSetupInterface $setup
     * @param string $existingTable
     * @return void
     */
    private function addReplicaTable(SchemaSetupInterface $setup, $existingTable)
    {
        $replicaTable = $existingTable . AbstractAction::REPLICA_SUFFIX;
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS %s LIKE %s',
            $setup->getConnection()->quoteIdentifier($setup->getTable($replicaTable)),
            $setup->getConnection()->quoteIdentifier($setup->getTable($existingTable))
        );
        $setup->getConnection()->query($sql);
    }
}
