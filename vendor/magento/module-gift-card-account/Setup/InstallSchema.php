<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Setup;

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
         * Create table 'magento_giftcardaccount'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftcardaccount')
        )->addColumn(
            'giftcardaccount_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Giftcardaccount Id'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Code'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
        )->addColumn(
            'date_created',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'Date Created'
        )->addColumn(
            'date_expires',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [],
            'Date Expires'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Balance'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'State'
        )->addColumn(
            'is_redeemable',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Redeemable'
        )->addIndex(
            $installer->getIdxName('magento_giftcardaccount', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('magento_giftcardaccount', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Giftcardaccount'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_giftcardaccount_pool'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftcardaccount_pool')
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'primary' => true],
            'Code'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Status'
        )->setComment(
            'Enterprise Giftcardaccount Pool'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_giftcardaccount_history'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_giftcardaccount_history')
        )->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'giftcardaccount_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Giftcardaccount Id'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Updated At'
        )->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Action'
        )->addColumn(
            'balance_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Balance Amount'
        )->addColumn(
            'balance_delta',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Balance Delta'
        )->addColumn(
            'additional_info',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Additional Info'
        )->addIndex(
            $installer->getIdxName('magento_giftcardaccount_history', ['giftcardaccount_id']),
            ['giftcardaccount_id']
        )->addForeignKey(
            $installer->getFkName(
                'magento_giftcardaccount_history',
                'giftcardaccount_id',
                'magento_giftcardaccount',
                'giftcardaccount_id'
            ),
            'giftcardaccount_id',
            $installer->getTable('magento_giftcardaccount'),
            'giftcardaccount_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Giftcardaccount History'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
