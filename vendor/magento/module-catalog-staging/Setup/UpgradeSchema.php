<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the CatalogStaging module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $this->addIndexIfNotExist(
                $setup,
                'catalog_product_entity',
                ['entity_id', 'created_in', 'updated_in']
            );
        }
        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $setup->getConnection()->dropForeignKey(
                $setup->getTable('catalog_product_index_price'),
                $setup->getFkName(
                    'catalog_product_index_price',
                    'entity_id',
                    'sequence_product',
                    'sequence_value'
                )
            );
        }
        $setup->endSetup();
    }

    /**
     * Adds an index to a table when the index is not exists
     *
     * @param SchemaSetupInterface $setup
     * @param string $table
     * @param array $idxFields
     * @return void
     */
    private function addIndexIfNotExist(SchemaSetupInterface $setup, $table, $idxFields)
    {
        $idxName = $setup->getIdxName($table, $idxFields);

        $tableName = $setup->getTable($table);
        $tableIndexes = array_column($setup->getConnection()->getIndexList($tableName), 'KEY_NAME');
        $hasIndex = in_array($idxName, $tableIndexes, true);

        if (!$hasIndex) {
            $setup->getConnection()->addIndex($tableName, $idxName, $idxFields);
        }
    }
}
