<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Block\Adminhtml\Banner\Edit;

/**
 * @api
 * @since 100.0.2
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize banner edit page tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('banner_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Banner Information'));
    }
}
