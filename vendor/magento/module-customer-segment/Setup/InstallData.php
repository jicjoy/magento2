<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSegment\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(CollectionFactory $collectionFactory, EavSetupFactory $eavSetupFactory)
    {
        $this->collectionFactory = $collectionFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();
        // use specific attributes for customer segments
        $attributesOfEntities = [
            'customer' => [
                'dob',
                'email',
                'firstname',
                'group_id',
                'lastname',
                'gender',
                'default_billing',
                'default_shipping',
                'created_at',
            ],
            'customer_address' => [
                'firstname',
                'lastname',
                'company',
                'street',
                'city',
                'region_id',
                'postcode',
                'country_id',
                'telephone',
            ],
            'order_address' => [
                'firstname',
                'lastname',
                'company',
                'street',
                'city',
                'region_id',
                'postcode',
                'country_id',
                'telephone',
                'email',
            ],
        ];

        foreach ($attributesOfEntities as $entityTypeId => $attributes) {
            foreach ($attributes as $attributeCode) {
                $eavSetup->updateAttribute($entityTypeId, $attributeCode, 'is_used_for_customer_segment', '1');
            }
        }

        /**
         * Resave all segments for segment conditions regeneration
         */
        $collection = $this->collectionFactory->create();
        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        foreach ($collection as $segment) {
            $segment->afterLoad();
            $segment->save();
        }

        $installer = $setup->createMigrationSetup();

        $installer->appendClassAliasReplace(
            'magento_customersegment_segment',
            'conditions_serialized',
            \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
            \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
            ['segment_id']
        );

        $installer->doUpdateClassAliases();

        $setup->endSetup();
    }
}
