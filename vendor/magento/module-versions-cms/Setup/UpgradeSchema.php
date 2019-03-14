<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the VersionCms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->removeVersionRevisionTables($setup);
        }

        $setup->endSetup();
    }

    /**
     * Remove version and revision data from DB
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function removeVersionRevisionTables(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $fields = [
            ['table' => 'cms_page', 'column' => 'published_revision_id'],
            ['table' => 'cms_page', 'column' => 'under_version_control'],
            ['table' => 'magento_versionscms_page_version'],
            ['table' => 'magento_versionscms_page_revision'],
        ];

        foreach ($fields as $filedInfo) {
            if (isset($filedInfo['column'])) {
                $connection->dropColumn($setup->getTable($filedInfo['table']), $filedInfo['column']);
            } else {
                $connection->dropTable($setup->getTable($filedInfo['table']));
            }
        }
    }
}
