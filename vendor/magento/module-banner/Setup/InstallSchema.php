<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Banner\Setup;

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
        $installer->startSetup();

        /**
         * Create table 'magento_banner'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_banner')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Banner Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'is_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Is Enabled'
        )->addColumn(
            'types',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Types'
        )->setComment(
            'Enterprise Banner'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_banner_content'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_banner_content')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Banner Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'banner_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Banner Content'
        )->addIndex(
            $installer->getIdxName('magento_banner_content', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('magento_banner_content', 'banner_id', 'magento_banner', 'banner_id'),
            'banner_id',
            $installer->getTable('magento_banner'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_banner_content', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Banner Content'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_banner_catalogrule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_banner_catalogrule')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Banner Id'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addIndex(
            $installer->getIdxName('magento_banner_catalogrule', ['rule_id']),
            ['rule_id']
        )->addForeignKey(
            $installer->getFkName('magento_banner_catalogrule', 'banner_id', 'magento_banner', 'banner_id'),
            'banner_id',
            $installer->getTable('magento_banner'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Banner Catalogrule'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_banner_salesrule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_banner_salesrule')
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Banner Id'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addIndex(
            $installer->getIdxName('magento_banner_salesrule', ['rule_id']),
            ['rule_id']
        )->addForeignKey(
            $installer->getFkName('magento_banner_salesrule', 'banner_id', 'magento_banner', 'banner_id'),
            'banner_id',
            $installer->getTable('magento_banner'),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Banner Salesrule'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
