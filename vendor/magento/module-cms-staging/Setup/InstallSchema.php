<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CmsStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Staging\Setup\BasicSetup;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var BasicSetup
     */
    protected $basicSetup;

    /**
     * @param BasicSetup $basicSetup
     */
    public function __construct(BasicSetup $basicSetup)
    {
        $this->basicSetup = $basicSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->basicSetup->install(
            $setup,
            'sequence_cms_page',
            'cms_page',
            'page_id',
            [
                [
                    'referenceTable' => 'cms_page_store',
                    'referenceColumn' => 'page_id',
                    'staged' => true,
                ]
            ]
        );
        $this->basicSetup->install(
            $setup,
            'sequence_cms_block',
            'cms_block',
            'block_id',
            [
                [
                    'referenceTable' => 'cms_block_store',
                    'referenceColumn' => 'block_id',
                    'staged' => true,
                ],
            ]
        );

        $installer->endSetup();
    }
}
