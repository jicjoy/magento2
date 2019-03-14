<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * @var EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);

        /**
         * Add attribute to the eav/attribute
         */
        $eavSetup->addAttribute(
            $entityTypeId,
            'automatic_sorting',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Automatic Sorting',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => 'none',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => 'category'
            ]
        );

        /**
         * Add attribute to the 'General Information' attribute group
         */
        $eavSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'general-information'),
            'automatic_sorting',
            null
        );
    }
}
