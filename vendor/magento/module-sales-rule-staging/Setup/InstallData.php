<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRuleStaging\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\SalesRuleStaging\Setup\SalesRuleMigrationFactory
     */
    private $salesRuleMigrationFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\SalesRuleStaging\Setup\SalesRuleMigrationFactory $salesRuleMigrationFactory
     */
    public function __construct(
        \Magento\SalesRuleStaging\Setup\SalesRuleMigrationFactory $salesRuleMigrationFactory
    ) {
        $this->salesRuleMigrationFactory = $salesRuleMigrationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\SalesRuleStaging\Setup\SalesRuleMigration $salesRuleMigration */
        $salesRuleMigration = $this->salesRuleMigrationFactory->create();

        // Migrate sales rules for staging
        $salesRuleMigration->migrateRules($setup);

        $setup->endSetup();
    }
}
