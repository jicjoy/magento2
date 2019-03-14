<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Banner\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Module\Setup\Migration;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->createMigrationSetup();
        $setup->startSetup();

        $installer->appendClassAliasReplace(
            'magento_banner_content',
            'banner_content',
            Migration::ENTITY_TYPE_BLOCK,
            Migration::FIELD_CONTENT_TYPE_WIKI,
            ['banner_id', 'store_id']
        );
        $installer->doUpdateClassAliases();

        $setup->endSetup();
    }
}
