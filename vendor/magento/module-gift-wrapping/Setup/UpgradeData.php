<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $entityAttributesCodes = [
                'gw_base_price_incl_tax' => 'decimal',
                'gw_price_incl_tax' => 'decimal',
                'gw_items_base_price_incl_tax' => 'decimal',
                'gw_items_price_incl_tax' => 'decimal',
                'gw_card_base_price_incl_tax' => 'decimal',
                'gw_card_price_incl_tax' => 'decimal',
            ];
            foreach ($entityAttributesCodes as $code => $type) {
                $quoteInstaller->addAttribute('quote', $code, ['type' => $type, 'visible' => false]);
                $quoteInstaller->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
            }
        }

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $groupName = 'Gift Options';

            $attributeSetId = $categorySetup->getDefaultAttributeSetId(Product::ENTITY);
            if (!$categorySetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $groupName)) {
                $categorySetup->addAttributeGroup(Product::ENTITY, $attributeSetId, $groupName, 60);
            }

            $attributesOrder = ['gift_wrapping_available' => 20, 'gift_wrapping_price' => 30];

            foreach ($attributesOrder as $key => $value) {
                $attribute = $salesInstaller->getAttribute($entityTypeId, $key);
                if ($attribute) {
                    $salesInstaller->addAttributeToGroup(
                        $entityTypeId,
                        $attributeSetId,
                        $groupName,
                        $attribute['attribute_id'],
                        $value
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $attribute = $categorySetup->getAttribute($entityTypeId, 'gift_wrapping_available');
            $categorySetup->updateAttribute(
                $entityTypeId,
                $attribute['attribute_id'],
                'source_model',
                \Magento\Catalog\Model\Product\Attribute\Source\Boolean::class
            );
        }

        if (version_compare($context->getVersion(), '2.1.3', '<')) {
            $this->changePriceAttributeDefaultScope($categorySetup, $entityTypeId);
        }
        $setup->endSetup();
    }

    /**
     * @param \Magento\Catalog\Setup\CategorySetup $categorySetup
     * @param int $entityTypeId
     * @return void
     */
    private function changePriceAttributeDefaultScope($categorySetup, $entityTypeId)
    {
        $attribute = $categorySetup->getAttribute($entityTypeId, 'gift_wrapping_price');
        $categorySetup->updateAttribute(
            $entityTypeId,
            $attribute['attribute_id'],
            'is_global',
            \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL
        );
    }
}
