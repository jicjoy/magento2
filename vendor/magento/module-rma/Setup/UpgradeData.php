<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Setup;

use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\DataConverter\SerializedToJson;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Sales setup factory
     *
     * @var RmaSetupFactory
     */
    protected $rmaSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @param RmaSetupFactory $rmaSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     */
    public function __construct(
        RmaSetupFactory $rmaSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        AggregatedFieldDataConverter $aggregatedFieldConverter
    ) {
        $this->rmaSetupFactory = $rmaSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var RmaSetup $rmaSetup */
        $rmaSetup = $this->rmaSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $rmaSetup->updateEntityTypes();
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $rmaSetup->addReturnableAttributeToGroup();
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            $this->convertSerializedToJson($setup, $context);
        }

        $this->eavConfig->clear();
        $setup->endSetup();
    }

    /**
     * Convert serialized to JSON-encoded data
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    private function convertSerializedToJson(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $fields = [
            new FieldToConvert(
                SerializedDataConverter::class,
                $setup->getTable('magento_rma_item_entity'),
                'entity_id',
                'product_options'
            ),
            new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('magento_rma_shipping_label'),
                'entity_id',
                'packages'
            ),
        ];
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $fields[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('magento_rma_item_eav_attribute'),
                'attribute_id',
                'validate_rules'
            );
        }
        $this->aggregatedFieldConverter->convert($fields, $setup->getConnection());
    }
}
