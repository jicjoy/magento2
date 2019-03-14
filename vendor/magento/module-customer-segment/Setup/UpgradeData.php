<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Setup;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\DB\FieldDataConverterFactory as ConverterFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson as Converter;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ConverterFactory
     */
    private $converterFactory;

    /**
     * @var State
     */
    private $state;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param ConverterFactory $converterFactory
     * @param State|null $state
     * @param CollectionFactory|null $collectionFactory
     */
    public function __construct(
        ConverterFactory $converterFactory,
        State $state = null,
        CollectionFactory $collectionFactory = null
    ) {
        $this->converterFactory = $converterFactory;
        $this->state = $state ?: ObjectManager::getInstance()
            ->get(State::class);
        $this->collectionFactory = $collectionFactory ?: ObjectManager::getInstance()
            ->get(CollectionFactory::class);
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->convertDataFromSerializedToJson($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->state->emulateAreaCode(
                FrontNameResolver::AREA_CODE,
                [$this, 'updateCustomerSegmentConditionSql']
            );
        }

        $setup->endSetup();
    }

    /**
     * Convert serialized data to JSON format.
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    public function convertDataFromSerializedToJson($setup)
    {
        $converter = $this->converterFactory->create(Converter::class);
        $converter->convert(
            $setup->getConnection(),
            $setup->getTable('magento_customersegment_segment'),
            'segment_id',
            'conditions_serialized'
        );
    }

    /**
     * Re-save existed customer segments to update condition SQL data for each segment
     *
     * @return void
     */
    public function updateCustomerSegmentConditionSql()
    {
        $collection = $this->collectionFactory->create();
        foreach ($collection as $segment) {
            /** @var \Magento\CustomerSegment\Model\Segment $segment */
            $segment->unsetData('condition_sql');
            $segment->save();
        }
    }
}
