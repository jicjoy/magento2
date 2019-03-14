<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\DB\FieldDataConverterFactory
     */
    private $fieldDataConverterFactory;

    /**
     * @var \Magento\Framework\DB\Select\QueryModifierFactory
     */
    private $queryModifierFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Framework\DB\FieldDataConverterFactory $fieldDataConverterFactory
     * @param \Magento\Framework\DB\Select\QueryModifierFactory $queryModifierFactory
     */
    public function __construct(
        \Magento\Framework\DB\FieldDataConverterFactory $fieldDataConverterFactory,
        \Magento\Framework\DB\Select\QueryModifierFactory $queryModifierFactory
    ) {
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory = $queryModifierFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->upgradeVersionTwoZeroOne($setup);
        }
    }

    /**
     * Upgrade data to version 2.0.1
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function upgradeVersionTwoZeroOne(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->fieldDataConverterFactory->create(
            \Magento\Cms\Setup\ContentConverter::class
        );

        $queryModifier = $this->queryModifierFactory->create(
            'like',
            [
                'values' => [
                    'banner_content' => '%conditions_encoded%'
                ]
            ]
        );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('magento_banner_content'),
            'banner_id',
            'banner_content',
            $queryModifier
        );
    }
}
