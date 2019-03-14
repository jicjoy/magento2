<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlist\Setup;

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
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $tableName = $installer->getTable('wishlist');

        $installer->getConnection()->dropForeignKey(
            $tableName,
            $installer->getFkName('wishlist', 'customer_id', 'customer_entity', 'entity_id')
        );
        $installer->getConnection()->dropIndex(
            $tableName,
            $installer->getIdxName(
                'wishlist',
                'customer_id',
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );

        $installer->getConnection()->addIndex(
            $tableName,
            $installer->getIdxName(
                'wishlist',
                'customer_id',
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            ),
            'customer_id',
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('wishlist', 'customer_id', 'customer_entity', 'entity_id'),
            $tableName,
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->addColumn(
            $tableName,
            'name',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Wish List Name',
                'default' => null
            ]
        );

        $installer->getConnection()->addColumn(
            $tableName,
            'visibility',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Wish List visibility type'
            ]
        );
    }
}
