<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Category;

class Tile extends \Magento\VisualMerchandiser\Controller\Adminhtml\Category\AbstractGrid
{
    /**
     * @var string
     */
    protected $blockClass = \Magento\VisualMerchandiser\Block\Adminhtml\Category\Merchandiser\Tile::class;

    /**
     * @var string
     */
    protected $blockName = 'tile';
}
