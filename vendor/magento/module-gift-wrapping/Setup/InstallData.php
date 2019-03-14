<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftWrapping\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;

/**
 * Class InstallData
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param ConfigInterface $productTypeConfig
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        ConfigInterface $productTypeConfig,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->productTypeConfig = $productTypeConfig;
        $this->categorySetupFactory = $categorySetupFactory;
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        /**
         * Add gift wrapping attributes for sales entities
         */
        $entityAttributesCodes = [
            'gw_id' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'gw_allow_gift_receipt' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'gw_add_card' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'gw_base_price' => 'decimal',
            'gw_price' => 'decimal',
            'gw_items_base_price' => 'decimal',
            'gw_items_price' => 'decimal',
            'gw_card_base_price' => 'decimal',
            'gw_card_price' => 'decimal',
            'gw_base_tax_amount' => 'decimal',
            'gw_tax_amount' => 'decimal',
            'gw_items_base_tax_amount' => 'decimal',
            'gw_items_tax_amount' => 'decimal',
            'gw_card_base_tax_amount' => 'decimal',
            'gw_card_tax_amount' => 'decimal',
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

        $itemsAttributesCodes = [
            'gw_id' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'gw_base_price' => 'decimal',
            'gw_price' => 'decimal',
            'gw_base_tax_amount' => 'decimal',
            'gw_tax_amount' => 'decimal',
        ];
        foreach ($itemsAttributesCodes as $code => $type) {
            $quoteInstaller->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
            $quoteInstaller->addAttribute('quote_address_item', $code, ['type' => $type, 'visible' => false]);
            $salesInstaller->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
        }

        $entityAttributesCodes = [
            'gw_base_price_invoiced' => 'decimal',
            'gw_price_invoiced' => 'decimal',
            'gw_items_base_price_invoiced' => 'decimal',
            'gw_items_price_invoiced' => 'decimal',
            'gw_card_base_price_invoiced' => 'decimal',
            'gw_card_price_invoiced' => 'decimal',
            'gw_base_tax_amount_invoiced' => 'decimal',
            'gw_tax_amount_invoiced' => 'decimal',
            'gw_items_base_tax_invoiced' => 'decimal',
            'gw_items_tax_invoiced' => 'decimal',
            'gw_card_base_tax_invoiced' => 'decimal',
            'gw_card_tax_invoiced' => 'decimal',
            'gw_base_price_refunded' => 'decimal',
            'gw_price_refunded' => 'decimal',
            'gw_items_base_price_refunded' => 'decimal',
            'gw_items_price_refunded' => 'decimal',
            'gw_card_base_price_refunded' => 'decimal',
            'gw_card_price_refunded' => 'decimal',
            'gw_base_tax_amount_refunded' => 'decimal',
            'gw_tax_amount_refunded' => 'decimal',
            'gw_items_base_tax_refunded' => 'decimal',
            'gw_items_tax_refunded' => 'decimal',
            'gw_card_base_tax_refunded' => 'decimal',
            'gw_card_tax_refunded' => 'decimal',
        ];
        foreach ($entityAttributesCodes as $code => $type) {
            $salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
        }

        $itemsAttributesCodes = [
            'gw_base_price_invoiced' => 'decimal',
            'gw_price_invoiced' => 'decimal',
            'gw_base_tax_amount_invoiced' => 'decimal',
            'gw_tax_amount_invoiced' => 'decimal',
            'gw_base_price_refunded' => 'decimal',
            'gw_price_refunded' => 'decimal',
            'gw_base_tax_amount_refunded' => 'decimal',
            'gw_tax_amount_refunded' => 'decimal',
        ];
        foreach ($itemsAttributesCodes as $code => $type) {
            $salesInstaller->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
        }

        $entityAttributesCodes = [
            'gw_base_price' => 'decimal',
            'gw_price' => 'decimal',
            'gw_items_base_price' => 'decimal',
            'gw_items_price' => 'decimal',
            'gw_card_base_price' => 'decimal',
            'gw_card_price' => 'decimal',
            'gw_base_tax_amount' => 'decimal',
            'gw_tax_amount' => 'decimal',
            'gw_items_base_tax_amount' => 'decimal',
            'gw_items_tax_amount' => 'decimal',
            'gw_card_base_tax_amount' => 'decimal',
            'gw_card_tax_amount' => 'decimal',
        ];
        foreach ($entityAttributesCodes as $code => $type) {
            $salesInstaller->addAttribute('invoice', $code, ['type' => $type]);
            $salesInstaller->addAttribute('creditmemo', $code, ['type' => $type]);
        }

        /**
         * Add gift wrapping attributes for catalog product entity
         */
        $applyTo = join(',', $this->productTypeConfig->filter('is_real_product'));

        /** @var CategorySetup  $installer*/
        $installer = $this->categorySetupFactory->create(['resourceName' => 'catalog_setup', 'setup' => $setup]);

        $installer->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gift_wrapping_available',
            [
                'group' => 'Gift Options',
                'backend' => \Magento\Catalog\Model\Product\Attribute\Backend\Boolean::class,
                'frontend' => '',
                'label' => 'Allow Gift Wrapping',
                'input' => 'select',
                'source' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'apply_to' => $applyTo,
                'frontend_class' => 'hidden-for-virtual',
                'frontend_input_renderer' => \Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form\Config::class,
                'input_renderer' => \Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form\Config::class,
                'visible_on_front' => false
            ]
        );

        $installer->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gift_wrapping_price',
            [
                'group' => 'Gift Options',
                'type' => 'decimal',
                'backend' => \Magento\Catalog\Model\Product\Attribute\Backend\Price::class,
                'frontend' => '',
                'label' => 'Price for Gift Wrapping',
                'input' => 'price',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'apply_to' => $applyTo,
                'frontend_class' => 'hidden-for-virtual',
                'visible_on_front' => false
            ]
        );

        $groupName = 'Autosettings';
        $entityTypeId = $salesInstaller->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $salesInstaller->getAttributeSetId($entityTypeId, 'Default');

        $attributesOrder = ['gift_wrapping_available' => 70, 'gift_wrapping_price' => 80];

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
}
