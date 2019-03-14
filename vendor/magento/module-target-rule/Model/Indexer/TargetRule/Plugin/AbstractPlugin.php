<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

use Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Processor as ProductRuleProcessor;
use Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Processor as RuleProductProcessor;

abstract class AbstractPlugin
{
    /**
     * @var ProductRuleProcessor
     */
    protected $_productRuleindexer;

    /**
     * @var RuleProductProcessor
     */
    protected $_ruleProductIndexer;

    /**
     * @param ProductRuleProcessor $productRuleProcessor
     * @param RuleProductProcessor $ruleProductProcessor
     */
    public function __construct(ProductRuleProcessor $productRuleProcessor, RuleProductProcessor $ruleProductProcessor)
    {
        $this->_productRuleindexer = $productRuleProcessor;
        $this->_ruleProductIndexer = $ruleProductProcessor;
    }

    /**
     * Invalidate indexers
     *
     * @return $this
     */
    protected function invalidateIndexers()
    {
        $this->_productRuleindexer->markIndexerAsInvalid();
        $this->_ruleProductIndexer->markIndexerAsInvalid();
        return $this;
    }
}
