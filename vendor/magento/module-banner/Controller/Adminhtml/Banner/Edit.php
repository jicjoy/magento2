<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Edit extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Edit action
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $model = $this->_initBanner('id');

        if (!$model->getId() && $bannerId) {
            $this->messageManager->addError(__('This banner no longer exists.'));
            $this->_redirect('adminhtml/*/');
            return;
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Banners'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Banner')
        );

        $this->_addBreadcrumb(
            $bannerId ? __('Edit Banner') : __('New Banner'),
            $bannerId ? __('Edit Banner') : __('New Banner')
        );
        $this->_view->renderLayout();
    }
}
