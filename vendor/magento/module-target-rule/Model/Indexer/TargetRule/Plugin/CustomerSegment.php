<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class CustomerSegment extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting customer segment
     *
     * @param \Magento\CustomerSegment\Model\Segment $customerSegment
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function afterDelete(\Magento\CustomerSegment\Model\Segment $customerSegment)
    {
        $this->invalidateIndexers();
        return $customerSegment;
    }

    /**
     * Invalidate target rule indexer after changing customer segment
     *
     * @param \Magento\CustomerSegment\Model\Segment $customerSegment
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function afterSave(\Magento\CustomerSegment\Model\Segment $customerSegment)
    {
        if (!$customerSegment->isObjectNew()) {
            $this->invalidateIndexers();
        }
        return $customerSegment;
    }
}
