<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProductStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\CatalogStaging\Setup\CatalogProductSetup;

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
            $installer->getTable('catalog_product_super_link'),
            $installer->getFkName(
                'catalog_product_super_link',
                'parent_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addIndex(
            $installer->getTable('catalog_product_super_link'),
            $installer->getIdxName('catalog_product_super_link', ['parent_id']),
            ['parent_id']
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'catalog_product_super_link',
                'parent_id',
                'catalog_product_entity',
                'row_id'
            ),
            $installer->getTable('catalog_product_super_link'),
            'parent_id',
            $installer->getTable('catalog_product_entity'),
            'row_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('catalog_product_super_attribute'),
            $installer->getFkName(
                'catalog_product_super_attribute',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'catalog_product_super_attribute',
                'product_id',
                'catalog_product_entity',
                'row_id'
            ),
            $installer->getTable('catalog_product_super_attribute'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'row_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->endSetup();
    }
}
