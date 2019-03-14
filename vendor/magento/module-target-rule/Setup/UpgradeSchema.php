<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Setup;

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
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $tables = [
                'magento_targetrule_index',
                'magento_targetrule_index_crosssell',
                'magento_targetrule_index_related',
                'magento_targetrule_index_upsell',
            ];
            foreach ($tables as $table) {
                $setup->getConnection()->modifyColumn(
                    $setup->getTable($table),
                    'customer_group_id',
                    ['type' => 'integer', 'nullable' => false]
                );
            }
        }
        $setup->endSetup();
    }
}
