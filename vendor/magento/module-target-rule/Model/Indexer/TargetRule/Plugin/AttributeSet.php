<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class AttributeSet extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting attribute set
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    public function afterDelete(\Magento\Eav\Model\Entity\Attribute\Set $attributeSet)
    {
        $this->invalidateIndexers();
        return $attributeSet;
    }
}
