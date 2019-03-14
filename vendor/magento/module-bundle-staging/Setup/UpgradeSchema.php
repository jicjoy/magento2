<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleStaging\Setup;

use Magento\Framework\DB\Ddl\Sequence;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Sequence
     */
    private $sequence;

    /**
     * @param Sequence $sequence
     */
    public function __construct(Sequence $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            // Updating the 'catalog_product_bundle_option' table.
            $tableStatus = $connection->showTableStatus(
                $setup->getTable('catalog_product_bundle_option')
            );

            $connection->query(
                $this->sequence->getCreateSequenceDdl(
                    $setup->getTable('sequence_product_bundle_option'),
                    $tableStatus['Auto_increment']
                )
            );

            $connection->dropForeignKey(
                $setup->getTable('catalog_product_bundle_option_value'),
                $setup->getFkName(
                    'catalog_product_bundle_option_value',
                    'option_id',
                    'catalog_product_bundle_option',
                    'option_id'
                )
            );

            $connection->dropForeignKey(
                $setup->getTable('catalog_product_bundle_selection'),
                $setup->getFkName(
                    'catalog_product_bundle_selection',
                    'option_id',
                    'catalog_product_bundle_option',
                    'option_id'
                )
            );

            $connection->modifyColumn(
                $setup->getTable('catalog_product_bundle_option'),
                'option_id',
                [
                    'type' => 'integer',
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false,
                    'comment' => 'Option Id',
                ]
            );

            $connection->dropIndex(
                $setup->getTable('catalog_product_bundle_option'),
                $connection->getPrimaryKeyName(
                    $setup->getTable('catalog_product_bundle_option')
                )
            );

            $connection->addIndex(
                $setup->getTable('catalog_product_bundle_option'),
                $connection->getPrimaryKeyName(
                    $setup->getTable('catalog_product_bundle_option')
                ),
                ['option_id', 'parent_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_option',
                    'option_id',
                    'sequence_product_bundle_option',
                    'sequence_value'
                ),
                $setup->getTable('catalog_product_bundle_option'),
                'option_id',
                $setup->getTable('sequence_product_bundle_option'),
                'sequence_value',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_option_value',
                    'option_id',
                    'sequence_product_bundle_option',
                    'sequence_value'
                ),
                $setup->getTable('catalog_product_bundle_option_value'),
                'option_id',
                $setup->getTable('sequence_product_bundle_option'),
                'sequence_value',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_option_value',
                    'parent_product_id',
                    'catalog_product_entity',
                    'row_id'
                ),
                $setup->getTable('catalog_product_bundle_option_value'),
                'parent_product_id',
                $setup->getTable('catalog_product_entity'),
                'row_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_selection',
                    'option_id',
                    'sequence_product_bundle_option',
                    'sequence_value'
                ),
                $setup->getTable('catalog_product_bundle_selection'),
                'option_id',
                $setup->getTable('sequence_product_bundle_option'),
                'sequence_value',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            // Updating the 'catalog_product_bundle_selection' table.
            $tableStatus = $connection->showTableStatus(
                $setup->getTable('sequence_product_bundle_selection')
            );

            $connection->query(
                $this->sequence->getCreateSequenceDdl(
                    $setup->getTable('sequence_product_bundle_selection'),
                    $tableStatus['Auto_increment']
                )
            );

            $connection->dropForeignKey(
                $setup->getTable('catalog_product_bundle_selection_price'),
                $setup->getFkName(
                    'catalog_product_bundle_selection_price',
                    'selection_id',
                    'catalog_product_bundle_selection',
                    'selection_id'
                )
            );

            $connection->modifyColumn(
                $setup->getTable('catalog_product_bundle_selection'),
                'selection_id',
                [
                    'type' => 'integer',
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false,
                    'comment' => 'Selection Id',
                ]
            );

            $connection->modifyColumn(
                $setup->getTable('catalog_product_bundle_selection'),
                'parent_product_id',
                [
                    'type' => 'integer',
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Parent Product Id',
                    'after' => 'selection_id'
                ]
            );

            $connection->dropIndex(
                $setup->getTable('catalog_product_bundle_selection'),
                $connection->getPrimaryKeyName(
                    $setup->getTable('catalog_product_bundle_selection')
                )
            );

            $connection->addIndex(
                $setup->getTable('catalog_product_bundle_selection'),
                $connection->getPrimaryKeyName(
                    $setup->getTable('catalog_product_bundle_selection')
                ),
                ['selection_id', 'parent_product_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_selection',
                    'selection_id',
                    'sequence_product_bundle_selection',
                    'sequence_value'
                ),
                $setup->getTable('catalog_product_bundle_selection'),
                'selection_id',
                $setup->getTable('sequence_product_bundle_selection'),
                'sequence_value',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_selection',
                    'parent_product_id',
                    'catalog_product_entity',
                    'row_id'
                ),
                $setup->getTable('catalog_product_bundle_selection'),
                'parent_product_id',
                $setup->getTable('catalog_product_entity'),
                'row_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_selection_price',
                    'selection_id',
                    'sequence_product_bundle_selection',
                    'sequence_value'
                ),
                $setup->getTable('catalog_product_bundle_selection_price'),
                'selection_id',
                $setup->getTable('sequence_product_bundle_selection'),
                'sequence_value',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $connection->addForeignKey(
                $setup->getFkName(
                    'catalog_product_bundle_selection_price',
                    'parent_product_id',
                    'catalog_product_entity',
                    'row_id'
                ),
                $setup->getTable('catalog_product_bundle_selection_price'),
                'parent_product_id',
                $setup->getTable('catalog_product_entity'),
                'row_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }

        $setup->endSetup();
    }
}
