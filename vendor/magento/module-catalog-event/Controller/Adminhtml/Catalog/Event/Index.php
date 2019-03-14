<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event;

class Index extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
{
    /**
     * Events list action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Events'));
        $this->_view->renderLayout();
    }
}
