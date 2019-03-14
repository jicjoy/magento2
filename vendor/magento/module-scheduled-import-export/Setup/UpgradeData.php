<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ScheduledImportExport\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\DB\DataConverter\SerializedToJson;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * Constructor
     *
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     */
    public function __construct(AggregatedFieldDataConverter $aggregatedFieldConverter)
    {
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->convertSerializedDataToJson($setup);
        }
    }

    /**
     * Convert magento_scheduled_operations data from serialized to JSON format
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    public function convertSerializedDataToJson($setup)
    {
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable('magento_scheduled_operations'),
                    'id',
                    'file_info'
                ),
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable('magento_scheduled_operations'),
                    'id',
                    'entity_attributes'
                ),
            ],
            $setup->getConnection()
        );
    }
}
