<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Index extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Segments list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::customer_customersegment');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Segments'));
        $this->_view->renderLayout();
    }
}
