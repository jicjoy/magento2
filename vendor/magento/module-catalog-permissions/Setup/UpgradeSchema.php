<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogPermissions\Setup;

use Magento\CatalogPermissions\Model\Indexer\AbstractAction;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $tables = [
                'magento_catalogpermissions_index',
                'magento_catalogpermissions_index_product',
                'magento_catalogpermissions_index_product_tmp',
                'magento_catalogpermissions_index_tmp',
            ];
            foreach ($tables as $table) {
                $setup->getConnection()->modifyColumn(
                    $setup->getTable($table),
                    'customer_group_id',
                    ['type' => 'integer', 'nullable' => false, 'unsigned' => true]
                );
            }
        }
        $setup->endSetup();

        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $this->addReplicaTable($setup, AbstractAction::INDEX_TABLE);
            $this->addReplicaTable($setup, AbstractAction::INDEX_TABLE . AbstractAction::PRODUCT_SUFFIX);
        }
    }

    /**
     * Add replica table for existing one.
     *
     * @param SchemaSetupInterface $setup
     * @param string $existingTable
     * @return void
     */
    private function addReplicaTable(SchemaSetupInterface $setup, $existingTable)
    {
        $replicaTable = $existingTable . AbstractAction::REPLICA_SUFFIX;
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS %s LIKE %s',
            $setup->getConnection()->quoteIdentifier($setup->getTable($replicaTable)),
            $setup->getConnection()->quoteIdentifier($setup->getTable($existingTable))
        );
        $setup->getConnection()->query($sql);
    }
}
