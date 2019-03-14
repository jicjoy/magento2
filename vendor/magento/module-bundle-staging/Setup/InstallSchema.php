<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\BundleStaging\Setup;

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

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('catalog_product_bundle_option'),
            $installer->getFkName(
                'catalog_product_bundle_option',
                'parent_id',
                'catalog_product_entity',
                'entity_id'
            )
        );

        $installer->getConnection()->addForeignKey(
            $installer->getFkName('catalog_product_bundle_option', 'parent_id', 'catalog_product_entity', 'row_id'),
            $installer->getTable('catalog_product_bundle_option'),
            'parent_id',
            $installer->getTable('catalog_product_entity'),
            'row_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->endSetup();
    }
}
