<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CheckoutStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    const PREVIEW_QUOTA_TABLE = 'quote_preview';

    const ID_FIELD_NAME = 'quote_id';

    /**
     * @var string
     */
    private static $connectionName = 'checkout';

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->startSetup()
            ->getConnection(self::$connectionName)
            ->newTable($setup->getTable(self::PREVIEW_QUOTA_TABLE, self::$connectionName))
            ->addColumn(
                self::ID_FIELD_NAME,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Preview Quota Id'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable(self::PREVIEW_QUOTA_TABLE),
                    self::ID_FIELD_NAME,
                    $setup->getTable('quote', self::$connectionName),
                    'entity_id',
                    self::$connectionName
                ),
                self::ID_FIELD_NAME,
                $setup->getTable('quote', self::$connectionName),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Preview quotas list');

        $setup->getConnection(self::$connectionName)->createTable($table);

        $setup->endSetup();
    }
}
