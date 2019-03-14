<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Staging\Setup\BasicSetup;

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

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('magento_giftcard_amount'),
            $installer->getFkName(
                'magento_giftcard_amount',
                'entity_id',
                'catalog_product_entity',
                'entity_id'
            )
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable('magento_giftcard_amount'),
            'entity_id',
            'row_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Row id',
            ]
        );

        $installer->endSetup();
    }
}
