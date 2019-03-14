<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Upgrade catalog staging data
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\App\State $state
    ) {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '2.1.0') < 0) {
            // Emulate area for products update
            $this->state->emulateAreaCode(
                \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                [$this, 'updateProducts'],
                [$setup]
            );
        }

        $setup->endSetup();
    }

    /**
     * Change 'created_in' value from 0 => 1 to allow product editing.
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function updateProducts(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->update(
            $setup->getTable('catalog_product_entity'),
            [
                'created_in' => \Magento\Staging\Model\VersionManager::MIN_VERSION,
            ],
            ['created_in = ?' => 0]
        );
    }
}
