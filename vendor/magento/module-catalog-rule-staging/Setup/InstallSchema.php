<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogRuleStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Staging\Setup\BasicSetup
     */
    protected $basicSetup;

    /**
     * @param \Magento\Staging\Setup\BasicSetup $basicSetup
     */
    public function __construct(
        \Magento\Staging\Setup\BasicSetup $basicSetup
    ) {
        $this->basicSetup = $basicSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->basicSetup->install(
            $setup,
            'sequence_catalogrule',
            'catalogrule',
            'rule_id',
            [
                [
                    'referenceTable' => 'catalogrule_customer_group',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'catalogrule_group_website',
                    'referenceColumn' => 'rule_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'catalogrule_website',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ]
            ]
        );

        $setup->endSetup();
    }
}
