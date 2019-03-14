<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Banner extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Banner::magento_banner';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $registry)
    {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @return \Magento\Banner\Model\Banner $model
     */
    protected function _initBanner($idFieldName = 'banner_id')
    {
        $bannerId = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create(\Magento\Banner\Model\Banner::class);
        if ($bannerId) {
            $model->load($bannerId);
        }
        if (!$this->_registry->registry('current_banner')) {
            $this->_registry->register('current_banner', $model);
        }
        return $model;
    }
}
