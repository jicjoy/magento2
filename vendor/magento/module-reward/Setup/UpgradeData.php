<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * Constructor
     *
     * @param FieldDataConverterFactory $fieldDataConverterFactory
     */
    public function __construct(
        FieldDataConverterFactory $fieldDataConverterFactory
    ) {
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->upgradeToVersionTwoZeroTwo($setup);
        }
    }

    /**
     * Upgrade to version 2.0.2, convert data for additional_data field in magento_reward_history table from serialized
     * to JSON format
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeToVersionTwoZeroTwo(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(SerializedToJson::class);
        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('magento_reward_history'),
            'history_id',
            'additional_data'
        );
    }
}
