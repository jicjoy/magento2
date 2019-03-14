<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VersionsCms\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'magento_versionscms_page_version'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_page_version')
        )->addColumn(
            'version_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Version Id'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addColumn(
            'access_level',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            9,
            [],
            'Access Level'
        )->addColumn(
            'page_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Page Id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'User Id'
        )->addColumn(
            'revisions_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Revisions Count'
        )->addColumn(
            'version_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Version Number'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_version', ['page_id']),
            ['page_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_version', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_version', ['version_number']),
            ['version_number']
        )->addForeignKey(
            $installer->getFkName('magento_versionscms_page_version', 'page_id', 'cms_page', 'page_id'),
            'page_id',
            $installer->getTable('cms_page'),
            'page_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_versionscms_page_version', 'user_id', 'admin_user', 'user_id'),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->setComment(
            'Enterprise Cms Page Version'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_versionscms_page_revision'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_page_revision')
        )->addColumn(
            'revision_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Revision Id'
        )->addColumn(
            'version_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Version Id'
        )->addColumn(
            'page_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Page Id'
        )->addColumn(
            'page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Page Layout'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Meta Description'
        )->addColumn(
            'content_heading',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Content Heading'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Content'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Layout Update Xml'
        )->addColumn(
            'custom_theme',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'Custom Theme'
        )->addColumn(
            'custom_page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Custom Page Layout'
        )->addColumn(
            'custom_layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Custom Layout Update Xml'
        )->addColumn(
            'custom_theme_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [],
            'Custom Theme From'
        )->addColumn(
            'custom_theme_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [],
            'Custom Theme To'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'User Id'
        )->addColumn(
            'revision_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Revision Number'
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_revision', ['version_id']),
            ['version_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_revision', ['page_id']),
            ['page_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_revision', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_page_revision', ['revision_number']),
            ['revision_number']
        )->addForeignKey(
            $installer->getFkName('magento_versionscms_page_revision', 'page_id', 'cms_page', 'page_id'),
            'page_id',
            $installer->getTable('cms_page'),
            'page_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('magento_versionscms_page_revision', 'user_id', 'admin_user', 'user_id'),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName(
                'magento_versionscms_page_revision',
                'version_id',
                'magento_versionscms_page_version',
                'version_id'
            ),
            'version_id',
            $installer->getTable('magento_versionscms_page_version'),
            'version_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Cms Page Revision'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_versionscms_increment'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_increment')
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Increment Id'
        )->addColumn(
            'increment_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Increment Type'
        )->addColumn(
            'increment_node',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Increment Node'
        )->addColumn(
            'increment_level',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Increment Level'
        )->addColumn(
            'last_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Last Id'
        )->addIndex(
            $installer->getIdxName(
                'magento_versionscms_increment',
                ['increment_type', 'increment_node', 'increment_level'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_type', 'increment_node', 'increment_level'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Enterprise Cms Increment'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_versionscms_hierarchy_node'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_hierarchy_node')
        )->addColumn(
            'node_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Node Id'
        )->addColumn(
            'parent_node_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Parent Node Id'
        )->addColumn(
            'page_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Page Id'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'Identifier'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addColumn(
            'level',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Level'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Sort Order'
        )->addColumn(
            'request_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Request Url'
        )->addColumn(
            'xpath',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Xpath'
        )->addColumn(
            'scope',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            8,
            ['nullable' => false, 'default=>' => 'default'],
            'Scope: default|website|store'
        )->addColumn(
            'scope_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            8,
            ['nullable' => false, 'default=>' => '0', 'unsigned' => true],
            'Scope Id'
        )->addIndex(
            $installer->getIdxName(
                'magento_versionscms_hierarchy_node',
                ['request_url', 'scope', 'scope_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['request_url', 'scope', 'scope_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('magento_versionscms_hierarchy_node', ['parent_node_id']),
            ['parent_node_id']
        )->addIndex(
            $installer->getIdxName('magento_versionscms_hierarchy_node', ['page_id']),
            ['page_id']
        )->addForeignKey(
            $installer->getFkName(
                'magento_versionscms_hierarchy_node',
                'parent_node_id',
                'magento_versionscms_hierarchy_node',
                'node_id'
            ),
            'parent_node_id',
            $installer->getTable('magento_versionscms_hierarchy_node'),
            'node_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Cms Hierarchy Node'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_versionscms_hierarchy_metadata'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_hierarchy_metadata')
        )->addColumn(
            'node_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Node Id'
        )->addColumn(
            'meta_first_last',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Meta First Last'
        )->addColumn(
            'meta_next_previous',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Meta Next Previous'
        )->addColumn(
            'meta_chapter',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Meta Chapter'
        )->addColumn(
            'meta_section',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Meta Section'
        )->addColumn(
            'meta_cs_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Meta Cs Enabled'
        )->addColumn(
            'pager_visibility',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Pager Visibility'
        )->addColumn(
            'pager_frame',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Pager Frame'
        )->addColumn(
            'pager_jump',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Pager Jump'
        )->addColumn(
            'menu_visibility',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Menu Visibility'
        )->addColumn(
            'menu_excluded',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Menu Excluded'
        )->addColumn(
            'menu_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Menu Layout'
        )->addColumn(
            'menu_brief',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Menu Brief'
        )->addColumn(
            'menu_levels_down',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Menu Levels Down'
        )->addColumn(
            'menu_ordered',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Menu Ordered'
        )->addColumn(
            'menu_list_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Menu List Type'
        )->addColumn(
            'top_menu_visibility',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => null, 'unsigned' => true],
            'Top Menu Visibility'
        )->addColumn(
            'top_menu_excluded',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => null, 'unsigned' => true],
            'Top Menu Excluded'
        )->addForeignKey(
            $installer->getFkName(
                'magento_versionscms_hierarchy_metadata',
                'node_id',
                'magento_versionscms_hierarchy_node',
                'node_id'
            ),
            'node_id',
            $installer->getTable('magento_versionscms_hierarchy_node'),
            'node_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Enterprise Cms Hierarchy Metadata'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'magento_versionscms_hierarchy_lock'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magento_versionscms_hierarchy_lock')
        )->addColumn(
            'lock_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Lock Id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'User Id'
        )->addColumn(
            'user_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'User Name'
        )->addColumn(
            'session_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Session Id'
        )->addColumn(
            'started_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Started At'
        )->setComment(
            'Enterprise Cms Hierarchy Lock'
        );
        $installer->getConnection()->createTable($table);

        // Add fields for cms/page table
        $installer->getConnection()->addColumn(
            $installer->getTable('cms_page'),
            'published_revision_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Published Revision Id'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('cms_page'),
            'website_root',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '1',
                'comment' => 'Website Root'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('cms_page'),
            'under_version_control',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Under Version Control Flag'
            ]
        );

        $installer->endSetup();
    }
}
