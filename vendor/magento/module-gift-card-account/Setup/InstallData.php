<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
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
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(SalesSetupFactory $salesSetupFactory, QuoteSetupFactory $quoteSetupFactory)
    {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $installer->startSetup();
        // 0.0.1 => 0.0.2
        $quoteInstaller->addAttribute('quote', 'gift_cards', ['type' => 'text']);

        // 0.0.2 => 0.0.3
        $quoteInstaller->addAttribute('quote', 'gift_cards_amount', ['type' => 'decimal']);
        $quoteInstaller->addAttribute('quote', 'base_gift_cards_amount', ['type' => 'decimal']);

        $quoteInstaller->addAttribute('quote_address', 'gift_cards_amount', ['type' => 'decimal']);
        $quoteInstaller->addAttribute('quote_address', 'base_gift_cards_amount', ['type' => 'decimal']);

        $quoteInstaller->addAttribute('quote', 'gift_cards_amount_used', ['type' => 'decimal']);
        $quoteInstaller->addAttribute('quote', 'base_gift_cards_amount_used', ['type' => 'decimal']);

        // 0.0.3 => 0.0.4
        $quoteInstaller->addAttribute('quote_address', 'gift_cards', ['type' => 'text']);

        // 0.0.4 => 0.0.5
        $salesInstaller->addAttribute('order', 'gift_cards', ['type' => 'text']);
        $salesInstaller->addAttribute('order', 'base_gift_cards_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'gift_cards_amount', ['type' => 'decimal']);

        // 0.0.5 => 0.0.6
        $quoteInstaller->addAttribute('quote_address', 'used_gift_cards', ['type' => 'text']);

        // 0.0.9 => 0.0.9
        $salesInstaller->addAttribute('order', 'base_gift_cards_invoiced', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'gift_cards_invoiced', ['type' => 'decimal']);

        $salesInstaller->addAttribute('invoice', 'base_gift_cards_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('invoice', 'gift_cards_amount', ['type' => 'decimal']);

        // 0.0.11 => 0.0.12
        $salesInstaller->addAttribute('order', 'base_gift_cards_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'gift_cards_refunded', ['type' => 'decimal']);

        $salesInstaller->addAttribute('creditmemo', 'base_gift_cards_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'gift_cards_amount', ['type' => 'decimal']);

        $installer->endSetup();
    }
}
