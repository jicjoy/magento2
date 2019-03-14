<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Logging\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QueryModifierFactory
     */
    private $queryModifierFactory;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * UpgradeData constructor
     *
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     * @param QueryModifierFactory $queryModifierFactory
     */
    public function __construct(
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        QueryModifierFactory $queryModifierFactory
    ) {
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->queryModifierFactory = $queryModifierFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->convertDataSerializedToJson($setup, $context);
        }
    }

    /**
     * Convert serialized data into JSON-encoded
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function convertDataSerializedToJson(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $fields = [
            new FieldToConvert(
                ObjectConverter::class,
                $setup->getTable('magento_logging_event'),
                'log_id',
                'info'
            ),
            new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('magento_logging_event_changes'),
                'id',
                'original_data'
            ),
            new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('magento_logging_event_changes'),
                'id',
                'result_data'
            ),
        ];
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $queryModifier = $this->queryModifierFactory->create(
                'in',
                [
                    'values' => [
                        'path' => [
                            'admin/magento_logging/actions',
                        ]
                    ]
                ]
            );
            $fields[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('core_config_data'),
                'config_id',
                'value',
                $queryModifier
            );
        }

        $this->aggregatedFieldConverter->convert($fields, $setup->getConnection());
    }
}
