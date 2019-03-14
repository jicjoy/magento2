<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\RewardStaging\Setup;

use Magento\Framework\DB\Select;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\SalesRule\Api\Data\RuleInterface;

/**
 * Class to migrate data of Sales Rules Reward Points for staging
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     */
    public function __construct(
        MetadataPool $metadataPool
    ) {
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->dropForeignKey(
            $setup->getTable('magento_reward_salesrule'),
            $setup->getConnection()->getForeignKeyName(
                $setup->getTable('magento_reward_salesrule'),
                'rule_id',
                $setup->getTable('sequence_salesrule'),
                'sequence_value'
            )
        );

        $select = $setup->getConnection()->select()
            ->from(['rules' => $setup->getTable('salesrule')])
            ->reset(Select::COLUMNS)
            ->columns(['rules.rule_id', 'rules.row_id'])
            ->join(
                ['reward' => $setup->getTable('magento_reward_salesrule')],
                '`reward`.`rule_id` = `rules`.`rule_id`',
                'reward.points_delta'
            )
            ->setPart('disable_staging_preview', true);
        $deltas = $setup->getConnection()->fetchAll($select);

        $setup->getConnection()->delete($setup->getTable('magento_reward_salesrule'));

        $data = [];
        foreach ($deltas as $delta) {
            $data[] = [$delta['row_id'], $delta['points_delta']];
        }
        if (count($data)) {
            $setup->getConnection()->insertArray(
                $setup->getTable('magento_reward_salesrule'),
                ['rule_id', 'points_delta'],
                $data
            );
        }

        $metadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $setup->getConnection()->addForeignKey(
            $setup->getConnection()->getForeignKeyName(
                $setup->getTable('magento_reward_salesrule'),
                'rule_id',
                $metadata->getEntityTable(),
                $metadata->getLinkField()
            ),
            $setup->getTable('magento_reward_salesrule'),
            'rule_id',
            $setup->getTable($metadata->getEntityTable()),
            $metadata->getLinkField()
        );

        $setup->endSetup();
    }
}
