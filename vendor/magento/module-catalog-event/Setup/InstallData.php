<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogEvent\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @param BlockFactory $modelBlockFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $sales = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        $sales->addAttribute('quote_item', 'event_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER]);
        $quotes = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $quotes->addAttribute('order_item', 'event_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER]);

        $cmsBlock = [
            'title' => 'Catalog Events Lister',
            'identifier' => 'catalog_events_lister',
            'content' => '{{block class="Magento\\\\CatalogEvent\\\\Block\\\\Event\\\\Lister" '
                . 'name="catalog.event.lister" template="lister.phtml"}}',
            'is_active' => 1,
            'stores' => 0,
        ];

        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        $block->setData($cmsBlock)->save();
    }
}
