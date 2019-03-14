<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerBalance\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Framework\App\ResourceConnection;

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
     * @var Resource
     */
    protected $resource;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        ResourceConnection $resource
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->resource = $resource;
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        // Modify Sales Entities
        //  0.0.5 => 0.0.6
        // Renamed: base_customer_balance_amount_used => base_customer_bal_amount_used
        $quoteInstaller->addAttribute('quote', 'customer_balance_amount_used', ['type' => 'decimal']);
        $quoteInstaller->addAttribute('quote', 'base_customer_bal_amount_used', ['type' => 'decimal']);

        $quoteInstaller->addAttribute('quote_address', 'base_customer_balance_amount', ['type' => 'decimal']);
        $quoteInstaller->addAttribute('quote_address', 'customer_balance_amount', ['type' => 'decimal']);

        $salesInstaller->addAttribute('order', 'base_customer_balance_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'customer_balance_amount', ['type' => 'decimal']);

        $salesInstaller->addAttribute('order', 'base_customer_balance_invoiced', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'customer_balance_invoiced', ['type' => 'decimal']);

        $salesInstaller->addAttribute('order', 'base_customer_balance_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'customer_balance_refunded', ['type' => 'decimal']);

        $salesInstaller->addAttribute('invoice', 'base_customer_balance_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('invoice', 'customer_balance_amount', ['type' => 'decimal']);

        $salesInstaller->addAttribute('creditmemo', 'base_customer_balance_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'customer_balance_amount', ['type' => 'decimal']);

        // 0.0.6 => 0.0.7
        $quoteInstaller->addAttribute('quote', 'use_customer_balance', ['type' => 'integer']);

        // 0.0.9 => 0.0.10
        // Renamed: base_customer_balance_total_refunded    => bs_customer_bal_total_refunded
        // Renamed: length: customer_balance_total_refunded => customer_bal_total_refunded
        $salesInstaller->addAttribute('creditmemo', 'bs_customer_bal_total_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'customer_bal_total_refunded', ['type' => 'decimal']);

        $salesInstaller->addAttribute('order', 'bs_customer_bal_total_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'customer_bal_total_refunded', ['type' => 'decimal']);

        $this->resource->getConnection('sales_order')->addColumn(
            $setup->getTable('sales_order_grid'),
            'refunded_to_store_credit',
            [
                'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'LENGTH' => '12,4',
                'COMMENT' => 'Refund to Store Credit'
            ]
        );
        $this->resource->getConnection('sales_order')->addColumn(
            $setup->getTable('magento_sales_order_grid_archive'),
            'refunded_to_store_credit',
            [
                'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'LENGTH' => '12,4',
                'COMMENT' => 'Refund to Store Credit'
            ]
        );
    }
}
