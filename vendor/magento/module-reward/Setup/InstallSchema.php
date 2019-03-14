<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reward\Setup;

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
         * Create table 'magento_reward'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_reward')
        )->addColumn(
            'reward_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Reward Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Website Id'
        )->addColumn(
            'points_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Points Balance'
        )->addColumn(
            'website_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            3,
            [],
            'Website Currency Code'
        )->addIndex(
            $installer->getIdxName(
                'magento_reward',
                ['customer_id', 'website_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['customer_id', 'website_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('magento_reward', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('magento_reward', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Reward'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_reward_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_reward_history')
        )->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'reward_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Reward Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Action'
        )->addColumn(
            'entity',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Entity'
        )->addColumn(
            'points_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Points Balance'
        )->addColumn(
            'points_delta',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Points Delta'
        )->addColumn(
            'points_used',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Points Used'
        )->addColumn(
            'points_voided',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'after'     => 'points_used'
            ],
            'Points Voided'
        )->addColumn(
            'currency_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Currency Amount'
        )->addColumn(
            'currency_delta',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Currency Delta'
        )->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            5,
            ['nullable' => false],
            'Base Currency Code'
        )->addColumn(
            'additional_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Additional Data'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Comment'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Created At'
        )->addColumn(
            'expired_at_static',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Expired At Static'
        )->addColumn(
            'expired_at_dynamic',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Expired At Dynamic'
        )->addColumn(
            'is_expired',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Expired'
        )->addColumn(
            'is_duplicate_of',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Is Duplicate Of'
        )->addColumn(
            'notification_sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Notification Sent'
        )->addIndex(
            $installer->getIdxName('magento_reward_history', ['reward_id']),
            ['reward_id']
        )->addIndex(
            $installer->getIdxName('magento_reward_history', ['website_id']),
            ['website_id']
        )->addIndex(
            $installer->getIdxName('magento_reward_history', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('magento_reward_history', 'reward_id', 'magento_reward', 'reward_id'),
            'reward_id',
            $installer->getTable('magento_reward'),
            'reward_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_reward_history', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName('magento_reward_history', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Reward History'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_reward_rate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_reward_rate')
        )->addColumn(
            'rate_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rate Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer Group Id'
        )->addColumn(
            'direction',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Direction'
        )->addColumn(
            'points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Points'
        )->addColumn(
            'currency_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Currency Amount'
        )->addIndex(
            $installer->getIdxName(
                'magento_reward_rate',
                ['website_id', 'customer_group_id', 'direction'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['website_id', 'customer_group_id', 'direction'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('magento_reward_rate', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('magento_reward_rate', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Reward Rate'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_reward_salesrule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_reward_salesrule')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Rule Id'
        )->addColumn(
            'points_delta',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Points Delta'
        )->addIndex(
            $installer->getIdxName(
                'magento_reward_salesrule',
                ['rule_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['rule_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Enterprise Reward Reward Salesrule'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
