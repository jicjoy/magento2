<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class NewAction extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Create new invitation form
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
        $this->_view->renderLayout();
    }
}
