<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CmsStaging\Setup;

use Magento\CmsStaging\Setup\CmsSetup;
use Magento\CmsStaging\Setup\CmsSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CmsSetupFactory
     */
    protected $cmsSetupFactory;

    /**
     * @param CmsSetupFactory $cmsSetupFactory
     */
    public function __construct(
        CmsSetupFactory $cmsSetupFactory
    ) {
        $this->cmsSetupFactory = $cmsSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var CmsSetup $cmsSetup */
        $cmsSetup = $this->cmsSetupFactory->create();
        $cmsSetup->execute($setup);
    }
}
