<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\DownloadableStaging\Setup;

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
            $installer->getTable('downloadable_link'),
            $installer->getFkName(
                'downloadable_link',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'downloadable_link',
                'product_id',
                'catalog_product_entity',
                'row_id'
            ),
            $installer->getTable('downloadable_link'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'row_id'
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('downloadable_sample'),
            $installer->getFkName(
                'downloadable_sample',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'downloadable_sample',
                'product_id',
                'catalog_product_entity',
                'row_id'
            ),
            $installer->getTable('downloadable_sample'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'row_id'
        );

        $installer->endSetup();
    }
}
