<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $select = $setup->getConnection()->select()
                ->from(
                    false,
                    [
                        'event_id',
                        'category_id',
                        'date_start',
                        'date_end',
                        'display_state',
                        'sort_order',
                        'status' => new \Zend_Db_Expr(
                            'if (date_start <= now() AND date_end >= now(), 0, if (date_end < now(), 2, 1))'
                        ),
                    ]
                );
            $select = $setup->getConnection()->updateFromSelect(
                $select,
                $setup->getTable('magento_catalogevent_event')
            );
            $setup->getConnection()->query($select);
        }
        $setup->endSetup();
    }
}
