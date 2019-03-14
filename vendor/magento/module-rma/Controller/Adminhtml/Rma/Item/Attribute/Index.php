<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Index extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Returns Attributes'));
        $this->_view->renderLayout();
    }
}
