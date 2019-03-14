<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reminder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->changeDateToDateTimeFields($setup);
        }

        $setup->endSetup();
    }

    /**
     * Change date time fields into datetime
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function changeDateToDateTimeFields(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = 'magento_reminder_rule';

        $connection->changeColumn(
            $setup->getTable($table),
            'from_date',
            'from_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'comment' => 'Active From'
            ]
        );

        $connection->changeColumn(
            $setup->getTable($table),
            'to_date',
            'to_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'comment' => 'Active To'
            ]
        );
    }
}
