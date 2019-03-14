<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Setup;

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
         * Create table 'magento_giftwrapping'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftwrapping')
        )->addColumn(
            'wrapping_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Wrapping Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Status'
        )->addColumn(
            'base_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Base Price'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Image'
        )->addIndex(
            $installer->getIdxName('magento_giftwrapping', ['status']),
            ['status']
        )->setComment(
            'Enterprise Gift Wrapping Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_giftwrapping_store_attributes'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftwrapping_store_attributes')
        )->addColumn(
            'wrapping_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Wrapping Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store Id'
        )->addColumn(
            'design',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Design'
        )->addIndex(
            $installer->getIdxName('magento_giftwrapping_store_attributes', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                'magento_giftwrapping_store_attributes',
                'wrapping_id',
                'magento_giftwrapping',
                'wrapping_id'
            ),
            'wrapping_id',
            $installer->getTable('magento_giftwrapping'),
            'wrapping_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_giftwrapping_store_attributes', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Gift Wrapping Attribute Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_giftwrapping_website'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftwrapping_website')
        )->addColumn(
            'wrapping_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Wrapping Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName('magento_giftwrapping_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('magento_giftwrapping_website', 'wrapping_id', 'magento_giftwrapping', 'wrapping_id'),
            'wrapping_id',
            $installer->getTable('magento_giftwrapping'),
            'wrapping_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_giftwrapping_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Gift Wrapping Website Table'
        );
        $installer->getConnection()->createTable($table);
    }
}
