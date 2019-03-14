<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reward\Controller\Adminhtml\Customer\Reward;

/**
 * @codeCoverageIgnore
 */
class History extends \Magento\Reward\Controller\Adminhtml\Customer\Reward
{
    /**
     * History Ajax Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
