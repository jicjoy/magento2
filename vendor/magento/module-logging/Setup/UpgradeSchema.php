<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Logging\Setup;

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
            $this->convertInfoFieldToTextFormat($setup);
        }
        $setup->endSetup();
    }

    /**
     * Convert 'info' field from 'varchar' to 'text' datatype
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function convertInfoFieldToTextFormat(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('magento_logging_event'),
            'info',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT]
        );
    }
}
