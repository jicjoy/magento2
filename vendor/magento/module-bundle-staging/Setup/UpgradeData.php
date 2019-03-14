<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleStaging\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            // Synchronizing the 'sequence_product_bundle_option' table.
            $connection->query(
                $connection->insertFromSelect(
                    $connection->select()
                        ->distinct()
                        ->from(
                            $setup->getTable('catalog_product_bundle_option'),
                            ['option_id']
                        ),
                    $setup->getTable('sequence_product_bundle_option'),
                    ['sequence_value'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
                )
            );

            // Synchronizing the 'sequence_product_bundle_selection' table.
            $connection->query(
                $connection->insertFromSelect(
                    $connection->select()->from(
                        $setup->getTable('catalog_product_bundle_selection'),
                        new \Zend_Db_Expr('DISTINCT `selection_id`')
                    ),
                    $setup->getTable('sequence_product_bundle_selection'),
                    ['sequence_value'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
                )
            );
        }

        $setup->endSetup();
    }
}
