<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class Category extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterDelete(\Magento\Catalog\Model\Category $category)
    {
        $this->invalidateIndexers();
        return $category;
    }

    /**
     * Invalidate target rule indexer after changing category products
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(\Magento\Catalog\Model\Category $category)
    {
        $isChangedProductList = $category->getData('is_changed_product_list');
        if ($isChangedProductList) {
            $this->invalidateIndexers();
        }
        return $category;
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
