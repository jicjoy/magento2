<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\DB\FieldDataConverterFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * UpgradeData constructor.
     *
     * @param FieldDataConverterFactory $fieldDataConverterFactory
     */
    public function __construct(FieldDataConverterFactory $fieldDataConverterFactory)
    {
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->convertSerializedDataToJson($setup);
        }

        $setup->endSetup();
    }

    /**
     * Convert metadata from serialized to JSON format.
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    public function convertSerializedDataToJson($setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(ReportConverter::class);

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('support_report'),
            'report_id',
            'report_data'
        );
    }
}
