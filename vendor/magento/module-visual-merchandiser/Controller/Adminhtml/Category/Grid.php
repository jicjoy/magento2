<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Category;

class Grid extends \Magento\VisualMerchandiser\Controller\Adminhtml\Category\AbstractGrid
{
    /**
     * @var string
     */
    protected $blockClass = \Magento\VisualMerchandiser\Block\Adminhtml\Category\Merchandiser\Grid::class;

    /**
     * @var string
     */
    protected $blockName = 'grid';
}
