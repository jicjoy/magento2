<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Setup;

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
    private static $quoteConnectionName = 'checkout';

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
        $installer = $setup;

        /**
         * Create table 'magento_customercustomattributes_sales_flat_order'
         */
        $table = $installer->getConnection(self::$salesConnectionName)->newTable(
            $installer->getTable('magento_customercustomattributes_sales_flat_order', self::$salesConnectionName)
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $installer->getFkName(
                'magento_customercustomattributes_sales_flat_order',
                'entity_id',
                'sales_order',
                'entity_id',
                self::$salesConnectionName
            ),
            'entity_id',
            $installer->getTable('sales_order', self::$salesConnectionName),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Customer Sales Flat Order'
        );
        $installer->getConnection(self::$salesConnectionName)->createTable($table);

        /**
         * Create table 'magento_customercustomattributes_sales_flat_order_address'
         */
        $table = $installer->getConnection(self::$salesConnectionName)->newTable(
            $installer->getTable(
                'magento_customercustomattributes_sales_flat_order_address',
                self::$salesConnectionName
            )
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $installer->getFkName(
                'magento_customercustomattributes_sales_flat_order_address',
                'entity_id',
                'sales_order_address',
                'entity_id',
                self::$salesConnectionName
            ),
            'entity_id',
            $installer->getTable('sales_order_address', self::$salesConnectionName),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Customer Sales Flat Order Address'
        );
        $installer->getConnection(self::$salesConnectionName)->createTable($table);

        /**
         * Create table 'magento_customercustomattributes_sales_flat_quote'
         */
        $table = $installer->getConnection(self::$quoteConnectionName)->newTable(
            $installer->getTable('magento_customercustomattributes_sales_flat_quote', self::$quoteConnectionName)
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $installer->getFkName(
                'magento_customercustomattributes_sales_flat_quote',
                'entity_id',
                'quote',
                'entity_id',
                self::$quoteConnectionName
            ),
            'entity_id',
            $installer->getTable('quote', self::$quoteConnectionName),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Customer Sales Flat Quote'
        );
        $installer->getConnection(self::$quoteConnectionName)->createTable($table);

        /**
         * Create table 'magento_customercustomattributes_sales_flat_quote_address'
         */
        $table = $installer->getConnection(self::$quoteConnectionName)->newTable(
            $installer->getTable(
                'magento_customercustomattributes_sales_flat_quote_address',
                self::$quoteConnectionName
            )
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $installer->getFkName(
                'magento_customercustomattributes_sales_flat_quote_address',
                'entity_id',
                'quote_address',
                'address_id',
                self::$quoteConnectionName
            ),
            'entity_id',
            $installer->getTable('quote_address', self::$quoteConnectionName),
            'address_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Customer Sales Flat Quote Address'
        );
        $installer->getConnection(self::$quoteConnectionName)->createTable($table);
    }
}
