<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class CatalogRuleGrid extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Banner catalog rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     * @return void
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(__('This banner does not exist.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Banners'));
        $this->_view->getLayout()->getBlock(
            'banner_catalogrule_grid'
        )->setSelectedCatalogRules(
            $this->getRequest()->getPost('selected_catalogrules')
        );
        $this->_view->renderLayout();
    }
}
