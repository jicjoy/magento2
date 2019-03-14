<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\CatalogRuleStaging\Setup\CatalogRuleSetupFactory
     */
    private $catalogRuleSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\CatalogRuleStaging\Setup\CatalogRuleSetupFactory $catalogRuleSetupFactory
     */
    public function __construct(
        \Magento\CatalogRuleStaging\Setup\CatalogRuleSetupFactory $catalogRuleSetupFactory
    ) {
        $this->catalogRuleSetupFactory = $catalogRuleSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\CatalogRuleStaging\Setup\CatalogRuleSetup $catalogRuleSetup */
        $catalogRuleSetup = $this->catalogRuleSetupFactory->create();

        // Migrate catalog rules for staging
        $catalogRuleSetup->migrateRules($setup);

        $setup->endSetup();
    }
}
