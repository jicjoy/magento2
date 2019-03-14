<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Staging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

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
         * Create table 'staging_update'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('staging_update')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Update ID'
        )->addColumn(
            'start_time',
            Table::TYPE_DATETIME,
            null,
            [],
            'Update start time'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Update name'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            255,
            [],
            'Update description'
        )->addColumn(
            'rollback_id',
            Table::TYPE_BIGINT,
            null,
            ['unsigned' => true],
            'Rollback ID'
        )->addColumn(
            'is_campaign',
            Table::TYPE_BOOLEAN,
            null,
            [],
            'Is update a campaign'
        )->addColumn(
            'is_rollback',
            Table::TYPE_BOOLEAN,
            null,
            [],
            'Is update a rollback'
        )->addColumn(
            'moved_to',
            Table::TYPE_BIGINT,
            null,
            ['unsigned' => true],
            'Update Id it was moved to'
        )->addIndex(
            $installer->getIdxName('staging_update', ['is_campaign']),
            ['is_campaign']
        )->addIndex(
            $installer->getIdxName(
                'staging_update_grid',
                ['name', 'description'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name', 'description'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Staging Updates table'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
