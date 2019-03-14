<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation;

class Index extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation
{
    /**
     * General report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magento_Invitation::report_magento_invitation_general'
        )->_addBreadcrumb(
            __('General Report'),
            __('General Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invitations Report'));
        $this->_view->renderLayout();
    }
}
