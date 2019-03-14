<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogEvent\Setup;

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
         * Create table 'magento_catalogevent_event'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogevent_event'))
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event Id'
            )
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Category Id'
            )
            ->addColumn(
                'date_start',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Date Start'
            )
            ->addColumn(
                'date_end',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Date End'
            )
            ->addColumn(
                'display_state',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Display State'
            )
            ->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Sort Order'
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogevent_event', ['category_id'], true),
                ['category_id'],
                ['type' => 'unique']
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogevent_event', ['date_start', 'date_end']),
                ['date_start', 'date_end']
            )
            ->setComment('Enterprise Catalogevent Event');

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'magento_catalogevent_event_image'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('magento_catalogevent_event_image'))
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Image'
            )
            ->addIndex(
                $setup->getIdxName('magento_catalogevent_event_image', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'magento_catalogevent_event_image',
                    'event_id',
                    'magento_catalogevent_event',
                    'event_id'
                ),
                'event_id',
                $setup->getTable('magento_catalogevent_event'),
                'event_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('magento_catalogevent_event_image', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Enterprise Catalogevent Event Image');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
