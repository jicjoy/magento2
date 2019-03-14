<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Returns;

class History extends \Magento\Rma\Controller\Returns
{
    /**
     * Customer returns history
     *
     * @return false|null
     */
    public function execute()
    {
        if (!$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Returns'));

        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}
