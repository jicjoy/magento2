<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRuleStaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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
            'sequence_salesrule',
            'salesrule',
            'rule_id',
            [
                [
                    'referenceTable' => 'salesrule_customer_group',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'salesrule_website',
                    'referenceColumn' => 'rule_id',
                    'staged' => true
                ],
                [
                    'referenceTable' => 'salesrule_product_attribute',
                    'referenceColumn' => 'rule_id',
                    'staged' => true,
                ],
                [
                    'referenceTable' => 'salesrule_label',
                    'referenceColumn' => 'rule_id',
                    'staged' => false
                ],
                [
                    'referenceTable' => 'salesrule_coupon',
                    'referenceColumn' => 'rule_id',
                    'staged' => false,
                ],
                [
                    'referenceTable' => 'salesrule_customer',
                    'referenceColumn' => 'rule_id',
                    'staged' => false,
                ],
            ]
        );

        $setup->endSetup();
    }
}
