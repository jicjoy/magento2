<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Sequence as DdlSequence;
use Magento\Staging\Setup\BasicSetup;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var DdlSequence
     */
    protected $ddlSequence;

    /**
     * @var BasicSetup
     */
    protected $basicSetup;

    /**
     * @param DdlSequence $ddlSequence
     * @param BasicSetup $basicSetup
     */
    public function __construct(
        DdlSequence $ddlSequence,
        BasicSetup $basicSetup
    ) {
        $this->ddlSequence = $ddlSequence;
        $this->basicSetup = $basicSetup;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->basicSetup->install(
            $setup,
            'sequence_catalog_category',
            'catalog_category_entity',
            'entity_id',
            [
                [
                    'referenceTable' => 'catalog_category_entity_datetime',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_category_entity_decimal',
                    'referenceColumn' => 'entity_id',
                    'staged' => true,
                ],
                [
                    'referenceTable' => 'catalog_category_entity_int',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_category_entity_text',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_category_entity_varchar',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ]
            ]
        );

        $this->basicSetup->install(
            $setup,
            'sequence_product',
            'catalog_product_entity',
            'entity_id',
            [
                [
                    'referenceTable' => 'catalog_product_entity_int',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_datetime',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_decimal',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_text',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_varchar',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_gallery',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_media_gallery_value_to_entity',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_entity_media_gallery_value',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_category_product',
                    'referenceColumn' => 'product_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_compare_item',
                    'referenceColumn' => 'product_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_product_website',
                    'referenceColumn' => 'product_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_product_link',
                    'referenceColumn' => 'linked_product_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_product_entity_tier_price',
                    'referenceColumn' => 'entity_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalog_product_index_price',
                    'referenceColumn' => 'entity_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_product_index_tier_price',
                    'referenceColumn' => 'entity_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalog_product_relation',
                    'referenceColumn' => 'child_id',
                    'staged' => false
                ],
            ]
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('catalog_product_option'),
            $installer->getFkName('catalog_product_option', 'product_id', 'catalog_product_entity', 'entity_id')
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('catalog_product_option', 'product_id', 'catalog_product_entity', 'row_id'),
            $installer->getTable('catalog_product_option'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'row_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable('catalog_product_link'),
            $installer->getFkName('catalog_product_link', 'product_id', 'catalog_product_entity', 'entity_id')
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('catalog_product_link', 'product_id', 'catalog_product_entity', 'row_id'),
            $installer->getTable('catalog_product_link'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'row_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->endSetup();
    }
}
