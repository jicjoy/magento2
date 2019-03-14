<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Setup;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // add config attributes to catalog product
        $eavSetup->addAttribute(
            'catalog_product',
            'related_tgtr_position_limit',
            [
                'label' => 'Related Target Rule Rule Based Positions',
                'visible' => false,
                'user_defined' => false,
                'required' => false,
                'type' => 'int',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'input' => 'text',
                'backend' => \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule::class
            ]
        );

        $eavSetup->addAttribute(
            'catalog_product',
            'related_tgtr_position_behavior',
            [
                'label' => 'Related Target Rule Position Behavior',
                'visible' => false,
                'user_defined' => false,
                'required' => false,
                'type' => 'int',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'input' => 'text',
                'backend' => \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule::class
            ]
        );

        $eavSetup->addAttribute(
            'catalog_product',
            'upsell_tgtr_position_limit',
            [
                'label' => 'Upsell Target Rule Rule Based Positions',
                'visible' => false,
                'user_defined' => false,
                'required' => false,
                'type' => 'int',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'input' => 'text',
                'backend' => \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule::class
            ]
        );

        $eavSetup->addAttribute(
            'catalog_product',
            'upsell_tgtr_position_behavior',
            [
                'label' => 'Upsell Target Rule Position Behavior',
                'visible' => false,
                'user_defined' => false,
                'required' => false,
                'type' => 'int',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'input' => 'text',
                'backend' => \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule::class
            ]
        );
    }
}
