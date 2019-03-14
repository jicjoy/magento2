<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $connection = $setup->getConnection();
            $connection->dropForeignKey(
                $setup->getTable('catalogrule_group_website'),
                $setup->getFkName(
                    'catalogrule_group_website',
                    'rule_id',
                    'sequence_catalogrule',
                    'sequence_value'
                )
            );
        }
        $setup->endSetup();
    }
}
