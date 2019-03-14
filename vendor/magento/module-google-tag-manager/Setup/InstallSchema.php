<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GoogleTagManager\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        if (!$this->moduleManager->isEnabled('Magento_Banner')) {
            return;
        }
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('magento_banner'),
            'is_ga_enabled',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => false,
                'default' => 0,
                'unsigned' => true,
                'comment' => 'Is Google Analytics Universals enabled'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('magento_banner'),
            'ga_creative',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Google Analytics Universals code'
            ]
        );

        $installer->endSetup();
    }
}
