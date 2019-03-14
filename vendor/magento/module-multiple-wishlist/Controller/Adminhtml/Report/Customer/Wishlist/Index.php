<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\MultipleWishlist\Controller\Adminhtml\Report\Customer\Wishlist
{
    /**
     * Index Action.
     * Forward to Wishlist Action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('wishlist');
        return $resultForward;
    }
}
