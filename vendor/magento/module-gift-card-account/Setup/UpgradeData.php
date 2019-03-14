<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftCardAccount\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Upgrade data for GiftCardAccount module.
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        AggregatedFieldDataConverter $aggregatedFieldConverter
    ) {
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeToVersionTwoZeroOne($setup);
        }

        $setup->endSetup();
    }

    /**
     * Upgrade to version 2.0.1, convert data from php-serialized to JSON format for:
     *  - `gift_cards` field in `quote_address`, `quote` and `sales_order` tables
     *  - `used_gift_cards` field in `quote_address` table
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroOne(ModuleDataSetupInterface $setup)
    {
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);

        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $quoteSetup->getTable('quote_address'),
                    'address_id',
                    'gift_cards'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $quoteSetup->getTable('quote_address'),
                    'address_id',
                    'used_gift_cards'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $quoteSetup->getTable('quote'),
                    'entity_id',
                    'gift_cards'
                ),
            ],
            $quoteSetup->getConnection()
        );
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $salesSetup->getTable('sales_order'),
                    'entity_id',
                    'gift_cards'
                ),
            ],
            $salesSetup->getConnection()
        );
    }
}
