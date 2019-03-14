<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class Index extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Rules list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Reminder::promo_reminder');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Email Reminders'));
        $this->_view->renderLayout();
    }
}
