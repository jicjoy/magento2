<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment;

class Segment extends \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
{
    /**
     * Segment Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Segment Report'));
        $this->_view->renderLayout();
    }
}
