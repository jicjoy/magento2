<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdvancedSalesRule\Setup;

use Magento\Framework\App\Area;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Magento\AdvancedSalesRule\Model\Indexer\SalesRule\Processor;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Processor
     */
    protected $indexerProcessor;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param Processor $indexerProcessor
     * @param State $appState
     */
    public function __construct(
        Processor $indexerProcessor,
        State $appState
    ) {
        $this->indexerProcessor = $indexerProcessor;
        $this->appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(
            Area::AREA_GLOBAL,
            function () {
                $this->indexerProcessor->reindexAll();
            }
        );
    }
}
