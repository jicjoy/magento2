<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Invitation\Setup;

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

        $customerGroupTable = $setup->getConnection()->describeTable($setup->getTable('customer_group'));
        $customerGroupIdType = $customerGroupTable['customer_group_id']['DATA_TYPE'] == 'int'
            ? \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER : $customerGroupTable['customer_group_id']['DATA_TYPE'];
        /**
         * Create table 'magento_invitation'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_invitation')
        )->addColumn(
            'invitation_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Invitation Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'invitation_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Invitation Date'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Email'
        )->addColumn(
            'referral_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Referral Id'
        )->addColumn(
            'protection_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Protection Code'
        )->addColumn(
            'signup_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Signup Date'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'group_id',
            $customerGroupIdType,
            null,
            ['unsigned' => true],
            'Group Id'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Message'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            ['nullable' => false, 'default' => 'new'],
            'Status'
        )->addIndex(
            $installer->getIdxName('magento_invitation', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('magento_invitation', ['referral_id']),
            ['referral_id']
        )->addIndex(
            $installer->getIdxName('magento_invitation', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('magento_invitation', ['group_id']),
            ['group_id']
        )->addForeignKey(
            $installer->getFkName('magento_invitation', 'group_id', 'customer_group', 'customer_group_id'),
            'group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName('magento_invitation', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName('magento_invitation', 'referral_id', 'customer_entity', 'entity_id'),
            'referral_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName('magento_invitation', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Invitation'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_invitation_status_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_invitation_status_history')
        )->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'invitation_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Invitation Id'
        )->addColumn(
            'invitation_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Invitation Date'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            ['nullable' => false, 'default' => 'new'],
            'Invitation Status'
        )->addIndex(
            $installer->getIdxName('magento_invitation_status_history', ['invitation_id']),
            ['invitation_id']
        )->addForeignKey(
            $installer->getFkName(
                'magento_invitation_status_history',
                'invitation_id',
                'magento_invitation',
                'invitation_id'
            ),
            'invitation_id',
            $installer->getTable('magento_invitation'),
            'invitation_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Invitation Status History'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_invitation_track'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_invitation_track')
        )->addColumn(
            'track_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Track Id'
        )->addColumn(
            'inviter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Inviter Id'
        )->addColumn(
            'referral_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Referral Id'
        )->addIndex(
            $installer->getIdxName(
                'magento_invitation_track',
                ['inviter_id', 'referral_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['inviter_id', 'referral_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('magento_invitation_track', ['referral_id']),
            ['referral_id']
        )->addForeignKey(
            $installer->getFkName('magento_invitation_track', 'inviter_id', 'customer_entity', 'entity_id'),
            'inviter_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_invitation_track', 'referral_id', 'customer_entity', 'entity_id'),
            'referral_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Invitation Track'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
