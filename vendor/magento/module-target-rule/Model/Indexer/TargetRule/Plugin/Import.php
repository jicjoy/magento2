<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

use Magento\ImportExport\Model\Import as ImportModel;

class Import extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer
     *
     * @param ImportModel $subject
     * @param bool $result
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportSource(ImportModel $subject, $result)
    {
        $this->invalidateIndexers();
        return $result;
    }

    /**
     * Invalidate indexers
     *
     * @return $this
     */
    protected function invalidateIndexers()
    {
        if (!$this->_productRuleindexer->isIndexerScheduled()) {
            $this->_productRuleindexer->markIndexerAsInvalid();
        }
        if (!$this->_ruleProductIndexer->isIndexerScheduled()) {
            $this->_ruleProductIndexer->markIndexerAsInvalid();
        }

        return $this;
    }
}
